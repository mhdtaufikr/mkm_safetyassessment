<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\RiskAssessment;
use App\Mail\RiskAssessmentSubmittedMail;
use Illuminate\Support\Facades\Mail;


class FormController extends Controller
{
    // Tampilkan form input risk assessment
    public function index()
    {
        $shops = Shop::all();
        return view('form.index', compact('shops'));
    }

    // Simpan data risk assessment dari form
    public function store(Request $request)
    {
        // ── Validasi dasar ────────────────────────────────────────
        $request->validate([
            'shop_id'       => 'required|exists:shops,id',
            'accessor_main' => 'required|string|max:255',
        ]);

        // ── Ambil semua array input ───────────────────────────────
        $scopeNumbers  = $request->input('scope_number', []);
        $findings      = $request->input('finding_problem', []);
        $hazards       = $request->input('potential_hazards', []);
        $accessors     = $request->input('accessor', []);
        $severities    = $request->input('severity', []);
        $possibilities = $request->input('possibility', []);
        $proposals     = $request->input('risk_reduction_proposal', []);
        $accessorMain  = trim($request->input('accessor_main'));

        $count           = count($scopeNumbers);
        $saved           = 0;
        $firstAssessment = null;

        for ($i = 0; $i < $count; $i++) {

            // ── Skip entry yang benar-benar kosong semua ──────────
            $isEmpty = empty($scopeNumbers[$i])
                    && empty(trim($findings[$i]      ?? ''))
                    && empty(trim($hazards[$i]       ?? ''))
                    && empty($severities[$i])
                    && empty($possibilities[$i])
                    && empty(trim($proposals[$i]     ?? ''));

            if ($isEmpty) continue;

            // ── Partial entry — ada isi tapi tidak lengkap ────────
            if (
                empty($scopeNumbers[$i])          ||
                empty(trim($findings[$i]  ?? '')) ||
                empty(trim($hazards[$i]   ?? '')) ||
                empty($severities[$i])            ||
                empty($possibilities[$i])         ||
                empty(trim($proposals[$i] ?? ''))
            ) {
                return back()
                    ->withInput()
                    ->withErrors(['entries' => "Entry #" . ($i + 1) . ": semua field wajib diisi lengkap."]);
            }

            // ── Hitung score & risk level di server (tidak percaya frontend) ──
            $severityVal    = (int) $severities[$i];
            $possibilityVal = (int) $possibilities[$i];
            $score          = $severityVal * $possibilityVal;

            if      ($score > 16) $riskLevel = 'Extreme';
            elseif  ($score >= 10) $riskLevel = 'High';
            elseif  ($score >= 5)  $riskLevel = 'Medium';
            elseif  ($score > 0)   $riskLevel = 'Low';
            else                   $riskLevel = '';

            // ── Upload file (opsional) ────────────────────────────
            $filePath = null;
            if ($request->hasFile("file.$i")) {
                $file = $request->file("file.$i");

                // Validasi file per-entry
                if (!$file->isValid()) {
                    return back()
                        ->withInput()
                        ->withErrors(['entries' => "Entry #" . ($i + 1) . ": file tidak valid."]);
                }

                $allowedMimes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xlsx', 'xls'];
                if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedMimes)) {
                    return back()
                        ->withInput()
                        ->withErrors(['entries' => "Entry #" . ($i + 1) . ": tipe file tidak diizinkan."]);
                }

                $filePath = $file->store('risk_files', 'public');
            }

            // ── Tentukan accessor: dari hidden input, fallback ke accessor_main ──
            $accessor = trim($accessors[$i] ?? '') ?: $accessorMain;

            // ── Simpan ke database ────────────────────────────────
            $assessment = RiskAssessment::create([
                'shop_id'                 => $request->shop_id,
                'scope_number'            => (int) $scopeNumbers[$i],
                'finding_problem'         => trim($findings[$i]),
                'potential_hazards'       => trim($hazards[$i]),
                'accessor'                => $accessor,
                'severity'                => $severityVal,
                'possibility'             => $possibilityVal,
                'score'                   => $score,
                'risk_level'              => $riskLevel,
                'risk_reduction_proposal' => trim($proposals[$i]),
                'file'                    => $filePath,
                'date'                    => now()->toDateString(), // ✅ pakai server time
            ]);

            if (!$firstAssessment) {
                $firstAssessment = $assessment;
            }

            $saved++;
        }

        // ── Tidak ada entry valid yang tersimpan ──────────────────
        if ($saved === 0) {
            return back()
                ->withInput()
                ->withErrors(['entries' => 'Minimal harus ada 1 entry yang diisi sebelum submit.']);
        }

        // ── Kirim email notifikasi (tidak block redirect kalau gagal) ──
        if ($firstAssessment) {
            try {
                Mail::to('wiwit.sabdo@ptmkm.co.id')
                    ->cc(['muhammad.taufik@ptmkm.co.id'])
                    ->send(new RiskAssessmentSubmittedMail($firstAssessment));
            } catch (\Throwable $e) {
                \Log::warning('Risk assessment mail failed: ' . $e->getMessage());
                // Tidak re-throw — data sudah tersimpan, tetap lanjut redirect
            }
        }

        // ── Redirect ke halaman QR dengan flash success ───────────
        $shopName = \App\Models\Shop::where('id', $request->shop_id)->value('name');

        return redirect('/qr/' . rawurlencode($shopName))
               ->with('success', "Risk assessment berhasil disimpan! ({$saved} entry tersimpan)");
    }



    public function indexShop($name)
{
    // Decode dari URL (misalnya: Rear%20Axle jadi Rear Axle)
    $decodedName = urldecode($name);

    // Cari berdasarkan nama
    $shop = Shop::where('name', $decodedName)->firstOrFail();

    // Cek nama file gambar berdaasassarkan nama shop (opsional, disesuaikan)
    $shopImage = strtolower(str_replace(' ', '_', $shop->name)) . '.png';

    return view('form.shop', [
    'shopName' => $shop->name,
    'shopId' => $shop->id,
    'shopImage' => $shop->image, // Ambil dari DB, bukan dari manipulasi nama manual
    'shopUpdatedAt' => $shop->updated_at,
]);

}


    public function qrScan($name)
{
    return view('form.scan',compact('name'));
}

}
