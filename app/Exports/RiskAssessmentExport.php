<?php

namespace App\Exports;

use App\Models\SaFinding;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RiskAssessmentExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return SaFinding::with(['shop', 'assessment'])->get()->map(function ($item) {
            return [
                $item->id,
                $item->assessment->shop->name ?? 'N/A',
                $item->assessment->scope_number ?? 'N/A',
                $item->assessment->finding_problem ?? 'N/A',
                $item->assessment->potential_hazards ?? 'N/A',
                $item->assessment->accessor ?? 'N/A',
                $item->assessment->severity ?? 'N/A',
                $item->assessment->possibility ?? 'N/A',
                $item->assessment->score ?? 'N/A',
                $item->assessment->risk_level ?? 'N/A',
                $item->assessment->risk_reduction_proposal ?? 'N/A',
                isset($item->assessment->is_followed_up)
                    ? ($item->assessment->is_followed_up ? 'Close' : 'Open')
                    : 'N/A',
                optional($item->assessment?->created_at)->format('Y-m-d') ?? 'N/A',
                $item->countermeasure,
                $item->genba_date,
                $item->pic_area,
                $item->pic_repair,
                $item->due_date,
                $item->status,
                $item->progress_date,
                $item->checked,
                $item->code,
                $item->file ? asset('storage/' . $item->file) : 'No file',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Finding ID',
            'Shop',
            'Scope',
            'Problem',
            'Hazard',
            'Acessor',
            'Severity',
            'Probability',
            'Score',
            'Risk Level',
            'Reduction Measures',
            'Status',
            'Created At',
            'Countermeasure',
            'Genba Date',
            'PIC Area',
            'PIC Repair',
            'Due Date',
            'Follow-up Status',
            'Progress Date',
            'Checked',
            'Code',
            'File',
        ];
    }
}

