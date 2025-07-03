<?php

namespace App\Exports;

use App\Models\SaFinding;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;

class RiskAssessmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithDrawings
{
    protected $data;

    public function __construct()
    {
        $this->data = SaFinding::with(['shop', 'assessment.shop'])->get();
    }

    public function collection()
    {
        return $this->data;
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
            'File Before',
            'File After'
        ];
    }

    public function map($item): array
    {
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
            isset($item->assessment->is_followed_up) ? ($item->assessment->is_followed_up ? 'Close' : 'Open') : 'N/A',
            optional($item->assessment?->created_at)->format('Y-m-d') ?? 'N/A',
            $item->countermeasure ?? 'N/A',
            $item->genba_date ?? 'N/A',
            $item->pic_area ?? 'N/A',
            $item->pic_repair ?? 'N/A',
            $item->due_date ?? 'N/A',
            $item->status ?? 'N/A',
            $item->progress_date ?? 'N/A',
            $item->checked ?? 'N/A',
            $item->code ?? 'N/A',
            $item->file_before ? 'Before' : '',
            $item->file_after ? 'After' : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // Freeze header
            $sheet->freezePane('A2');

            // Auto width for columns
            foreach (range('A', 'X') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Row height for all rows with data
            for ($i = 2; $i <= $highestRow; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(85);
            }

            // Border all cells
            $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Wrap text and vertical align top
            $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                ->getAlignment()
                ->setWrapText(true)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            // Center header
            $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal('center');
        }
    ];
}


    public function drawings()
    {
        $drawings = [];
        $row = 2;

        foreach ($this->data as $item) {
            // File Before
            if ($item->file_before) {
                $pathBefore = storage_path('app/public/' . $item->file_before);
                if (file_exists($pathBefore) && exif_imagetype($pathBefore)) {
                    $drawing = new Drawing();
                    $drawing->setName('Before');
                    $drawing->setDescription('Foto Sebelum');
                    $drawing->setPath($pathBefore);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('W' . $row);
                    $drawings[] = $drawing;
                }
            }

            // File After
            if ($item->file_after) {
                $pathAfter = storage_path('app/public/' . $item->file_after);
                if (file_exists($pathAfter) && exif_imagetype($pathAfter)) {
                    $drawing = new Drawing();
                    $drawing->setName('After');
                    $drawing->setDescription('Foto Sesudah');
                    $drawing->setPath($pathAfter);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('X' . $row);
                    $drawings[] = $drawing;
                }
            }

            $row++;
        }

        return $drawings;
    }
}
