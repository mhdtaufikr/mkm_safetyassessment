<?php

namespace App\Http\Controllers;

use App\Models\SaFinding;
use App\Models\RiskAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaFindingController extends Controller
{
    public function index($assessmentId)
    {
        $findings = SaFinding::where('id_assessment', $assessmentId)->get();
        $assessment = RiskAssessment::with('shop')->findOrFail($assessmentId);

        // Ambil file dari RiskAssessment yang sesuai shop dan memiliki file
        $formFiles = RiskAssessment::where('shop_id', $assessment->shop_id)
            ->whereNotNull('file')
            ->get();

        return view('formaction.action', [
            'findings' => $findings,
            'assessmentId' => $assessmentId,
            'shopId' => $assessment->shop_id,
            'shopName' => optional($assessment->shop)->name,
            'formFiles' => $formFiles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_assessment' => 'required|exists:risk_assessment_headers,id',
            'countermeasure' => 'required|string|max:255',
            'pic_area' => 'nullable|string|max:100',
            'pic_repair' => 'nullable|string|max:100',
            'due_date' => 'nullable|date',
            'shop' => 'required|string|max:100',
            'shop_id' => 'required|exists:shops,id',
            'genba_date' => 'nullable|date',
            'progress_date' => 'nullable|date',
            'checked' => 'required|in:YES,NO',
            'code' => 'nullable|string|max:50',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:10240',
        ]);

        // Cek duplikasi
        $existing = SaFinding::where('id_assessment', $request->id_assessment)->exists();
        if ($existing) {
            return redirect()->back()->with('error', 'Data temuan untuk assessment ini sudah pernah diinput.');
        }

        // Simpan file jika ada
        $filePath = null;
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->storeAs('sa_findings_file', $filename, 'public');
        }

        // Simpan temuan
        SaFinding::create([
            'id_assessment' => $request->id_assessment,
            'countermeasure' => $request->countermeasure,
            'pic_area' => $request->pic_area,
            'pic_repair' => $request->pic_repair,
            'due_date' => $request->due_date,
            'status' => 'Closed',
            'shop' => $request->shop,
            'shop_id' => $request->shop_id,
            'genba_date' => $request->genba_date,
            'progress_date' => $request->progress_date,
            'checked' => $request->checked,
            'code' => $request->code,
            'file' => $filePath,
        ]);

        // Update status assessment
        RiskAssessment::where('id', $request->id_assessment)->update([
            'is_followed_up' => true,
            'status' => 'Closed',
        ]);

        return redirect()->route('formAction.view', $request->id_assessment)
                         ->with('success', 'Action added successfully.');
    }

    public function followup($id)
    {
        $finding = SaFinding::find($id);
        if (!$finding) {
            return redirect()->back()->with('error', 'Temuan tidak ditemukan.');
        }

        return view('formaction.followup', compact('finding'));
    }

    public function view($assessmentId)
    {
        $assessment = RiskAssessment::with('shop')->findOrFail($assessmentId);
        $findings = SaFinding::where('id_assessment', $assessmentId)->get();

        return view('formaction.view', compact('assessment', 'findings'));
    }
}
