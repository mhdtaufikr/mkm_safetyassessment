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
    $request->validate([
        'shop_id' => 'required|exists:shops,id',
        'scope_number' => 'required|array',
        'finding_problem' => 'required|array',
        'potential_hazards' => 'required|array',
        'accessor' => 'required|array',
        'severity' => 'required|array',
        'possibility' => 'required|array',
        'score' => 'required|array',
        'risk_level' => 'required|array',
        'risk_reduction_proposal' => 'required|array',
        'file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:10240',
    ]);

    $count = count($request->scope_number);
    $firstAssessment = null;

    for ($i = 0; $i < $count; $i++) {
        $filePath = null;

        if ($request->hasFile("file.$i")) {
            $filePath = $request->file("file.$i")->store('risk_files', 'public');
        }

        $assessment = RiskAssessment::create([
            'shop_id' => $request->shop_id,
            'scope_number' => $request->scope_number[$i],
            'finding_problem' => $request->finding_problem[$i],
            'potential_hazards' => $request->potential_hazards[$i],
            'accessor' => $request->accessor[$i],
            'severity' => $request->severity[$i],
            'possibility' => $request->possibility[$i],
            'score' => $request->score[$i],
            'risk_level' => $request->risk_level[$i],
            'risk_reduction_proposal' => $request->risk_reduction_proposal[$i],
            'file' => $filePath,
        ]);

        // Simpan salah satu untuk dikirimkan via email
        if (!$firstAssessment) {
            $firstAssessment = $assessment;
        }
    }

    // Kirim email notifikasi
    if ($firstAssessment) {
    Mail::to('dayennurhidayat@gmail.com')
       // ->cc(['muhammad.taufik@ptmkm.co.id', 'wiwit.sabdo@ptmkm.co.id'])
        ->send(new RiskAssessmentSubmittedMail($firstAssessment));
}

    return redirect()->back()->with('success', 'Risk assessment successfully saved and notification sent.');
}

    public function indexShop($name)
{
    // Decode dari URL (misalnya: Rear%20Axle jadi Rear Axle)
    $decodedName = urldecode($name);

    // Cari berdasarkan nama
    $shop = Shop::where('name', $decodedName)->firstOrFail();

    // Cek nama file gambar berdasarkan nama shop (opsional, disesuaikan)
    $shopImage = strtolower(str_replace(' ', '_', $shop->name)) . '.png';

    return view('form.shop', [
        'shopName' => $shop->name,
        'shopId' => $shop->id,
        'shopImage' => $shopImage // tambahkan variabel ini
    ]);
}


    public function qrScan($name)
{
    return view('form.scan',compact('name'));
}

}
