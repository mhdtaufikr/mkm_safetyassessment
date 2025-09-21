<?php

namespace App\Exports;

use App\Models\RiskAssessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class RiskAssessmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $data;

    protected $severityLabels = [
        1 => '1 - Insignificant',
        2 => '2 - Minor',
        3 => '3 - Moderate',
        4 => '4 - Major',
        5 => '5 - Catastrophic',
    ];

    protected $possibilityLabels = [
        1 => '1 - Rare',
        2 => '2 - Unlikely',
        3 => '3 - Possible',
        4 => '4 - Likely',
        5 => '5 - Almost Certain',
    ];

    protected $scopeLabels = [
        1 => '1 - Man',
        2 => '2 - Machine',
        3 => '3 - Method',
        4 => '4 - Material',
        5 => '5 - Environment',
    ];

    public function __construct()
    {
        // Mengambil data dan langsung mengurutkannya dari yang terbaru
      $this->data = RiskAssessment::with(['shop', 'finding'])
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->get();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Assessment ID',
            'Shop',
            'Scope',
            'Problem',
            'Hazard',
            'Accessor',
            'Severity',
            'Probability',
            'Score',
            'Risk Level',
            'Reduction Measures',
            'Follow-up Status',
            'Created At',
            'Countermeasure',
            'Genba Date',
            'PIC Area',
            'PIC Repair',
            'Due Date',
            'Status',
            'Progress Date',
            'Checked',
            'Code',
            'File Before',
            'File After',
        ];
    }

    public function map($item): array
    {
        $finding = $item->finding;

        return [
            $item->id,
            $item->shop->name ?? 'N/A',
            $this->scopeLabels[$item->scope_number ?? 0] ?? $item->scope_number ?? 'N/A',
            $item->finding_problem ?? 'N/A',
            $item->potential_hazards ?? 'N/A',
            $item->accessor ?? 'N/A',
            $this->severityLabels[$item->severity ?? 0] ?? $item->severity ?? 'N/A',
            $this->possibilityLabels[$item->possibility ?? 0] ?? $item->possibility ?? 'N/A',
            $item->score ?? 'N/A',
            $item->risk_level ?? 'N/A',
            $item->risk_reduction_proposal ?? 'N/A',
            isset($item->is_followed_up) ? ($item->is_followed_up ? 'Close' : 'Open') : 'Open',
            optional($item->created_at)->format('Y-m-d') ?? 'N/A',
            $finding?->countermeasure ?? 'N/A',
            $finding?->genba_date ?? 'N/A',
            $finding?->pic_area ?? 'N/A',
            $finding?->pic_repair ?? 'N/A',
            $finding?->due_date ?? 'N/A',
            $finding?->status ?? 'Open',
            $finding?->progress_date ?? 'N/A',
            $finding?->checked ?? 'N/A',
            $finding?->code ?? 'N/A',
            $item->file ? 'Before' : '',
            $finding?->file ? 'After' : '',
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
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Landscape & fit to page
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);

                // Margin
                $sheet->getPageMargins()
                    ->setTop(0.3)
                    ->setBottom(0.3)
                    ->setLeft(0.4)
                    ->setRight(0.4);

                // Freeze header
                $sheet->freezePane('A2');

                // Auto-size A to V, fixed width W and X
                foreach (range('A', 'V') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getColumnDimension('W')->setWidth(40);
                $sheet->getColumnDimension('X')->setWidth(40);

                // Row height
                for ($i = 2; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(85);
                }

                // Border & alignment
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getAlignment()->setWrapText(true)
                    ->setVertical('top')
                    ->setHorizontal('left');

                $sheet->getStyle("A1:{$highestColumn}1")
                    ->getAlignment()->setHorizontal('center');

                // Insert Images Landscape
                $row = 2;
                foreach ($this->data as $item) {
                    $finding = $item->finding;

                    if ($item->file) {
                        $pathBefore = storage_path('app/public/risk_files/' . basename($item->file));
                        if (file_exists($pathBefore) && exif_imagetype($pathBefore)) {
                            $drawing = new Drawing();
                            $drawing->setName('Before');
                            $drawing->setDescription('Foto Sebelum');
                            $drawing->setPath($pathBefore);
                            $drawing->setWidth(160); // Landscape width
                            $drawing->setCoordinates('W' . $row);
                            $drawing->setOffsetX(5);
                            $drawing->setWorksheet($sheet);
                        }
                    }

                    if ($finding && $finding->file) {
                        $pathAfter = storage_path('app/public/sa_findings_file/' . basename($finding->file));
                        if (file_exists($pathAfter) && exif_imagetype($pathAfter)) {
                            $drawing = new Drawing();
                            $drawing->setName('After');
                            $drawing->setDescription('Foto Sesudah');
                            $drawing->setPath($pathAfter);
                            $drawing->setWidth(160); // Landscape width
                            $drawing->setCoordinates('X' . $row);
                            $drawing->setOffsetX(5);
                            $drawing->setWorksheet($sheet);
                        }
                    }

                    $row++;
                }
            },
        ];
    }
}