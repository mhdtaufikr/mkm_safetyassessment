<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Saudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SauditController extends Controller
{
    public function index(Request $request)
    {
        $query = Saudit::query();

        // Filter berdasarkan bulan dan tahun jika diisi
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                ->whereYear('date', $request->year);
        }

        // Filter berdasarkan range tanggal jika ada
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('date', [$request->from, $request->to]);
        }

        $audits = $query->orderBy('created_at', 'desc')->get();

        $actualScores = [0, 0, 0, 0, 0]; // Sort, Set in Order, Shine, Standardize, Sustain
        $targetScores = [16, 16, 16, 16, 16];

        if ($audits->count() > 0) {
            $scoresByCategory = [];

            foreach ($audits as $audit) {
                $scoreData = is_string($audit->scores) ? json_decode($audit->scores, true) : $audit->scores;
                foreach ($scoreData as $data) {
                    $category = $data['category'] ?? null;
                    if ($category) {
                        $scoresByCategory[$category][] = $data['score'];
                    }
                }
            }

            $categories = ['Sort', 'Set in Order', 'Shine', 'Standardize', 'Sustain'];
            $averageScores = [];

            foreach ($categories as $i => $cat) {
                if (!empty($scoresByCategory[$cat])) {
                    $totalScore = array_sum($scoresByCategory[$cat]);
                    $totalCount = count($scoresByCategory[$cat]);
                    $average = $totalCount > 0 ? round($totalScore / $totalCount, 2) : 0;
                    $averageScores[$cat] = $average;
                } else {
                    $averageScores[$cat] = 0;
                }

                $actualScores[$i] = $averageScores[$cat];
            }
        }

        return view('saudit.index', compact('audits', 'actualScores', 'targetScores'))
            ->with('month', $request->month)
            ->with('year', $request->year);
    }

    private function mapIndexToCategory($index)
    {
        if ($index >= 1 && $index <= 5) return 'Sort';
        if ($index >= 6 && $index <= 10) return 'Set in Order';
        if ($index >= 11 && $index <= 15) return 'Shine';
        if ($index >= 16 && $index <= 20) return 'Standardize';
        if ($index >= 21 && $index <= 25) return 'Sustain';
        return null;
    }

    public function create()
    {
        $shops = Shop::all();
        return view('saudit.create', compact('shops'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $validated = $request->validate([
            'shop' => 'required|string|max:255',
            'date' => 'required|date',
            'auditor' => 'required|string|max:255',
            'comments' => 'nullable|string',
            'items' => 'required|array',
            'items.*.check_item' => 'required|string',
            'items.*.description' => 'required|string',
            // --- PERBAIKAN 1: Mengubah validasi skor menjadi 0-5 ---
            'items.*.score' => 'required|numeric|between:0,5',
            'items.*.comment' => 'nullable|string',
            'items.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:5120',
        ]);

        $scores = [];
        $totalScore = 0;
        $categoryScores = [];

        // Looping untuk setiap item pertanyaan dari form
        foreach ($validated['items'] as $index => $item) {
            $filePath = null;

            // Logika ini sudah benar, hanya memproses file jika ada
            if ($request->hasFile("items.$index.file")) {
                $uploadedFile = $request->file("items.$index.file");
                $newFileName = time() . '_' . Str::random(8) . '.' . $uploadedFile->getClientOriginalExtension();
                $filePath = $uploadedFile->storeAs('saudit_files', $newFileName, 'public');
            }

            // Memanggil helper function untuk menentukan kategori
            $category = $this->mapIndexToCategory($index);

            // Susun data skor untuk item ini
            $scores[$index] = [
                'score' => (int) $item['score'],
                'comment' => $item['comment'] ?? '',
                'check_item' => $item['check_item'],
                'description' => $item['description'],
                'file' => $filePath, // Akan null jika tidak ada file
                'category' => $category,
            ];

            // Kelompokkan skor berdasarkan kategori
            if ($category) {
                if (!isset($categoryScores[$category])) {
                    $categoryScores[$category] = [];
                }
                $categoryScores[$category][] = (int) $item['score'];
            }

            // Akumulasi total skor
            $totalScore += (int) $item['score'];
        }

        // Hitung rata-rata skor per kategori
        $averageByCategory = [];
        foreach ($categoryScores as $category => $scoresArray) {
            $averageByCategory[$category] = round(array_sum($scoresArray) / count($scoresArray), 2);
        }

        // --- PERBAIKAN 2: Mengubah perhitungan skor maksimal menjadi * 5 ---
        $maxTotal = count($validated['items']) * 5;
        $finalScore = $maxTotal > 0 ? ($totalScore / $maxTotal) * 100 : 0;

        // Simpan semua data ke database
        Saudit::create([
            'shop' => $validated['shop'],
            'date' => $validated['date'],
            'auditor' => $validated['auditor'],
            'scores' => $scores,
            'final_score' => $finalScore,
            'comments' => $validated['comments'],
            'score_by_category' => $averageByCategory,
        ]);

        // Redirect ke halaman QR dengan pesan sukses
        return redirect()->to('/qr/' . urlencode($validated['shop']))->with('success', '5S Audit Successfully Submitted.');
    }

    public function edit($id)
    {
        $audit = Saudit::findOrFail($id);
        $shops = Shop::all();
        return view('saudit.create', compact('audit', 'shops'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'shop' => 'required|string|max:255',
            'date' => 'required|date',
            'auditor' => 'required|string|max:255',
            'items' => 'required|array',
            'comments' => 'nullable|string',
            'items.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:5120',
        ]);

        $audit = Saudit::findOrFail($id);
        $existingScores = $audit->scores ?? [];
        $scores = [];
        $totalScore = 0;
        $categoryScores = [];

        foreach ($validated['items'] as $index => $item) {
            $existingFile = $existingScores[$index]['file'] ?? null;
            $filePath = $existingFile;

            if ($request->hasFile("items.$index.file")) {
                if ($existingFile) {
                    Storage::disk('public')->delete($existingFile);
                }
                $uploadedFile = $request->file("items.$index.file");
                $filePath = $uploadedFile->store('saudit_files', 'public');
            }

            $category = $this->mapIndexToCategory($index + 1);

            $scores[$index] = [
                'score' => (int) $item['score'],
                'comment' => $item['comment'] ?? '',
                'check_item' => $item['check_item'],
                'description' => $item['description'],
                'file' => $filePath,
                'category' => $category,
            ];

            if ($category) {
                $categoryScores[$category][] = (int) $item['score'];
            }

            $totalScore += (int) $item['score'];
        }

        $averageByCategory = [];
        foreach ($categoryScores as $category => $scoresArray) {
            $averageByCategory[$category] = round(array_sum($scoresArray) / count($scoresArray), 2);
        }

        $maxTotal = count($validated['items']) * 4;
        $finalScore = $maxTotal ? ($totalScore / $maxTotal) * 100 : 0;

        $audit->update([
            'shop' => $validated['shop'],
            'date' => $validated['date'],
            'auditor' => $validated['auditor'],
            'scores' => $scores,
            'final_score' => $finalScore,
            'comments' => $validated['comments'],
            'score_by_category' => $averageByCategory,
        ]);

        return redirect()->route('saudit.index')->with('success', '5S audit berhasil diperbarui.');
    }

    public function show($id)
    {
        $audit = Saudit::findOrFail($id);
        return view('saudit.view', compact('audit'));
    }

    public function destroy($id)
    {
        $audit = Saudit::findOrFail($id);
        foreach ($audit->scores as $score) {
            if (!empty($score['file'])) {
                Storage::disk('public')->delete($score['file']);
            }
        }
        $audit->delete();
        return redirect()->route('saudit.index')->with('success', '5S audit berhasil dihapus.');
    }

    public function createShop($name)
    {
        $decodedName = urldecode($name);
        $shop = Shop::where('name', $decodedName)->first();

        $shopImage = null;
        $shopUpdatedAt = now(); // ✅ default jika shop tidak ditemukan

        if ($shop) {
            $filename = strtolower(str_replace(' ', '_', $shop->name)) . '.png';
            $path = public_path('storage/shop_images/' . $filename);

            if (file_exists($path)) {
                $shopImage = $filename;
            }

            // ✅ update timestamp hanya jika ada
            $shopUpdatedAt = $shop->updated_at ?? now();
        }

        return view('saudit.createShop', [
            'name' => $decodedName,
            'shopImage' => $shopImage,
            'shopUpdatedAt' => $shopUpdatedAt, // ✅ pastikan dikirim ke view
        ]);
    }
}
