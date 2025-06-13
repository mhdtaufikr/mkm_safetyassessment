<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Saudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SauditController extends Controller
{
    public function index()
{
    $audits = Saudit::orderBy('created_at', 'desc')->get();

    $shops = $audits->groupBy('shop');

    $chartLabels = [];
    $chartData = [];

    foreach ($shops as $shop => $shopAudits) {
        $chartLabels[] = $shop;

        $totalFinalScore = $shopAudits->sum('final_score');
        $averageFinalScore = $shopAudits->count() > 0 ? $totalFinalScore / $shopAudits->count() : 0;

        $chartData[] = round($averageFinalScore, 2);
    }

    return view('saudit.index', compact('audits', 'chartLabels', 'chartData'));
}



    // Fungsi mapping index ke kategori
    private function mapIndexToCategory($index)
    {
        if ($index >= 1 && $index <= 4) return 'Sort';
        if ($index >= 5 && $index <= 8) return 'Set in Order';
        if ($index >= 9 && $index <= 12) return 'Shine';
        if ($index >= 13 && $index <= 16) return 'Standardize';
        if ($index >= 17 && $index <= 20) return 'Sustain';
        return null;
    }

    public function create()
    {
        $shops = Shop::all();
        return view('saudit.create', compact('shops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop' => 'required|string|max:255',
            'date' => 'required|date',
            'auditor' => 'required|string|max:255',
            'items' => 'required|array',
            'comments' => 'nullable|string',
            'items.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:5120',
        ]);

        $scores = [];
        $totalScore = 0;

        foreach ($validated['items'] as $index => $item) {
            $filePath = null;

            if ($request->hasFile("items.$index.file")) {
                $uploadedFile = $request->file("items.$index.file");
                $filePath = $uploadedFile->store('saudit_files', 'public');
            }

            $scores[$index] = [
                'score' => (int) $item['score'],
                'comment' => $item['comment'] ?? '',
                'check_item' => $item['check_item'],
                'description' => $item['description'],
                'file' => $filePath,
            ];

            $totalScore += (int) $item['score'];
        }

        $maxTotal = count($validated['items']) * 4;
        $finalScore = $maxTotal ? ($totalScore / $maxTotal) * 100 : 0;

        Saudit::create([
            'shop' => $validated['shop'],
            'date' => $validated['date'],
            'auditor' => $validated['auditor'],
            'scores' => $scores,
            'final_score' => $finalScore,
            'comments' => $validated['comments'],
        ]);

        return redirect()->route('saudit.index')->with('success', '5S audit successfully submitted.');
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

            $scores[$index] = [
                'score' => (int) $item['score'],
                'comment' => $item['comment'] ?? '',
                'check_item' => $item['check_item'],
                'description' => $item['description'],
                'file' => $filePath,
            ];

            $totalScore += (int) $item['score'];
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
        ]);

        return redirect()->route('saudit.index')->with('success', '5S audit successfully updated.');
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
        return redirect()->route('saudit.index')->with('success', '5S audit deleted.');
    }

    public function createShop($name)
    {
        return view('saudit.createShop', compact('name'));
    }
}
