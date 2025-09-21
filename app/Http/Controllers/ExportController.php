<?php

namespace App\Http\Controllers;

use App\Models\SaFinding;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiskAssessmentExport;
use PDF;

class ExportController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new RiskAssessmentExport, 'risk_assessments.xlsx');
    }

    public function exportPDF()
    {
        $assessments = SaFinding::with('shop')->get();
        $pdf = PDF::loadView('exports.risk_pdf', compact('assessments'));
        return $pdf->download('risk_assessments.pdf');
    }
}
