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
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RiskAssessmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $data;
    protected $start_date;
    protected $end_date;

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

    protected $number = 1;

    protected $color_risk_level = [
        'low'    => '00B050',
        'medium' => 'EDBB31',
        'high'   => 'ED7D31',
        'extreme' => 'FF0000'
    ];

    const NUMBER_WIDTH = 4;
    const SMALL_WIDTH = 12;
    const MEDIUM_WIDTH = 18;
    const LARGE_WIDTH = 40;


    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        // Mengambil data dan langsung mengurutkannya dari yang terbaru
        $this->data = RiskAssessment::with(['shop', 'finding'])
            ->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay()
            ])
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
            [
                'NO',
                'FINDING PROBLEM',
                'POTENTIAL HAZARDS',
                'COUNTERMEASURE',
                'GENBA DATE',
                'SHOP',
                'RESPONSIBILITY',
                null,
                null,
                'PROGRESS',
                null,
                null
            ],
            [
                null,
                null,
                null,
                null,
                null,
                null,
                'PIC Area',
                'PIC Repair',
                'Due Date',
                'Status',
                'Date',
                'Checked by',
            ]
        ];
    }

    public function map($item): array
    {
        $finding = $item->finding;

        return [
            $this->number++,
            $item->finding_problem ?? 'N/A',
            $item->potential_hazards ?? 'N/A',
            $finding?->countermeasure ?? 'N/A',
            optional($item->created_at)->format('d M Y') ?? 'N/A',
            $item->shop->name ?? 'N/A',
            $finding?->pic_area ?? 'N/A',
            $finding?->pic_repair ?? 'N/A',
            $finding?->due_date ?? 'N/A',
            $finding?->status ?? 'Open',
            $finding?->progress_date ?? 'N/A',
            $finding?->checked ?? 'N/A',
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
                $sheet->freezePane('B3');

                // set lebar kolom
                $sheet->getColumnDimension('A')->setWidth(self::NUMBER_WIDTH);
                $sheet->getColumnDimension('B')->setWidth(self::LARGE_WIDTH);
                $sheet->getColumnDimension('C')->setWidth(self::LARGE_WIDTH);
                $sheet->getColumnDimension('D')->setWidth(self::LARGE_WIDTH);
                $sheet->getColumnDimension('E')->setWidth(self::MEDIUM_WIDTH);
                $sheet->getColumnDimension('F')->setWidth(self::SMALL_WIDTH);
                $sheet->getColumnDimension('G')->setWidth(self::SMALL_WIDTH);
                $sheet->getColumnDimension('H')->setWidth(self::SMALL_WIDTH);
                $sheet->getColumnDimension('I')->setWidth(self::SMALL_WIDTH);
                $sheet->getColumnDimension('J')->setWidth(self::SMALL_WIDTH);
                $sheet->getColumnDimension('K')->setWidth(self::SMALL_WIDTH);
                $sheet->getColumnDimension('L')->setWidth(self::SMALL_WIDTH);

                // Row height
                for ($i = 3; $i <= $highestRow; $i++) {
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

                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');

                $sheet->mergeCells('G1:I1');
                $sheet->mergeCells('J1:L1');

                $sheet->getStyle('A1:L2')->getFont()->setBold(true);
                $sheet->getStyle('A1:L2')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A1:L2')->getAlignment()->setVertical('center');


                // Insert Images Landscape
                $row = 3;

                foreach ($this->data as $item) {
                    $finding = $item->finding;

                    // ================================
                    // 1. FOTO BEFORE → Kolom B
                    // ================================
                    $richText = new RichText();

                    // --- KALIMAT ATAS ---
                    $runFinding = $richText->createTextRun($item->finding_problem ?? 'N/A');
                    $runFinding->getFont()->setBold(false);
                    $runFinding->getFont()->setSize(11);
                    $runFinding->getFont()->setColor(new Color('000000'));

                    // --- SPACER UNTUK GAMBAR (AREA GAMBAR) ---
                    // ini “reservasi ruang” tempat gambar akan duduk
                    $richText->createText("\n\n\n\n\n\n\n\n\n\n\n");

                    // --- KALIMAT BAWAH ---
                    $runAccessor = $richText->createTextRun($item->accessor ?? 'N/A');
                    $runAccessor->getFont()->setBold(true);
                    $runAccessor->getFont()->setSize(10);
                    $runAccessor->getFont()->setItalic(true);
                    $runAccessor->getFont()->setColor(new Color('555555'));

                    // set ke cell
                    $sheet->setCellValue("B{$row}", $richText);

                    // alignment
                    $sheet->getStyle("B{$row}")
                        ->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(Alignment::VERTICAL_TOP);

                    // ================================
                    // 2. RISK LEVEL → Kolom C
                    // ================================
                    $potential = $item->potential_hazards ?? 'N/A';
                    $risk = $item->risk_level ?? 'N/A';

                    $riskKey = strtolower($risk);
                    $riskColor = $this->color_risk_level[$riskKey] ?? '000000';

                    $richText = new RichText();

                    $runPotential = $richText->createTextRun($potential);
                    $runPotential->getFont()->setBold(false);
                    $runPotential->getFont()->setSize(11);
                    $runPotential->getFont()->setColor(new Color('000000')); // hitam

                    $richText->createText("\n\n\n");

                    $runRisk = $richText->createTextRun($risk);
                    $runRisk->getFont()->setBold(true);
                    $runRisk->getFont()->setSize(20);
                    $runRisk->getFont()->setColor(new Color($riskColor)); // warna sesuai level

                    $sheet->setCellValue("C{$row}", $richText);

                    $sheet->getStyle("C{$row}")
                        ->getAlignment()->setWrapText(true);
                    $sheet->getStyle("C{$row}")
                        ->getAlignment()->setHorizontal('center');

                    if ($item->file) {
                        $pathBefore = storage_path('app/public/risk_files/' . basename($item->file));

                        if (file_exists($pathBefore) && exif_imagetype($pathBefore)) {
                            $drawing = new Drawing();
                            $drawing->setName('Before');
                            $drawing->setDescription('Foto Sebelum');
                            $drawing->setPath($pathBefore);
                            $drawing->setWidth(140);
                            $drawing->setCoordinates('B' . $row);
                            $drawing->setOffsetX(10);
                            $drawing->setOffsetY(50);
                            $drawing->setWorksheet($sheet);
                        }
                    }

                    // ================================
                    // 3. FOTO AFTER → Kolom D
                    // ================================
                    if ($finding && $finding->file) {
                        $pathAfter = storage_path('app/public/sa_findings_file/' . basename($finding->file));

                        if (file_exists($pathAfter) && exif_imagetype($pathAfter)) {
                            $drawing = new Drawing();
                            $drawing->setName('After');
                            $drawing->setDescription('Foto Sesudah');
                            $drawing->setPath($pathAfter);
                            $drawing->setWidth(140);
                            $drawing->setCoordinates('D' . $row);   // TARUH DI BARIS YANG SAMA
                            $drawing->setOffsetX(10);
                            $drawing->setOffsetY(50);
                            $drawing->setWorksheet($sheet);
                        }
                    }

                    // Status
                    $status = $finding->status ?? 'Open';
                    $richText = new RichText();
                    $statusColor = 'EDBB31';

                    $runStatus = $richText->createTextRun($status);
                    $runStatus->getFont()->setBold(true);
                    $runStatus->getFont()->setSize(20);
                    $runStatus->getFont()->setColor(new Color($statusColor));

                    $sheet->setCellValue("J{$row}", $richText);

                    $sheet->getStyle("J{$row}")
                        ->getAlignment()->setWrapText(true);
                    $sheet->getStyle("J{$row}")
                        ->getAlignment()->setHorizontal('center');

                    $sheet->getRowDimension($row)->setRowHeight(180);

                    $row++;
                }
            },
        ];
    }
}
