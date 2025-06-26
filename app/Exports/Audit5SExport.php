<?php

namespace App\Exports;

use App\Models\Saudit;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class Audit5SExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $audit;

    public function __construct($auditId)
    {
        $this->audit = Saudit::findOrFail($auditId);
    }

    public function array(): array
    {
        $rows = [];

        // Header Informasi
        $rows[] = ['Shop', $this->audit->shop];
        $rows[] = ['Date', \Carbon\Carbon::parse($this->audit->date)->format('d M Y')];
        $rows[] = ['Auditor', $this->audit->auditor];
        $rows[] = ['']; // Spasi kosong

        // Mapping kategori dan urutan
        $categories = [
            'Sort' => [],
            'Set In Order' => [],
            'Shine' => [],
            'Standardize' => [],
            'Sustain' => [],
        ];

        $map = [
            0 => 'Sort',
            1 => 'Sort',
            2 => 'Sort',
            3 => 'Sort', // Frequency
            4 => 'Sort',
            5 => 'Set In Order',
            6 => 'Set In Order',
            7 => 'Set In Order', // Storage Indicator
            8 => 'Set In Order',
            9 => 'Shine',
            10 => 'Shine',
            11 => 'Shine', // Cleaning Tools
            12 => 'Shine',
            13 => 'Standardize',
            14 => 'Standardize',
            15 => 'Standardize', // KPI
            16 => 'Standardize',
            17 => 'Sustain',
            18 => 'Sustain',
            19 => 'Sustain',
            20 => 'Sustain', // Check Sheet
        ];

        foreach ($this->audit->scores ?? [] as $i => $item) {
            $cat = $map[$i] ?? ($item['category'] ?? 'Uncategorized');
            if (!isset($categories[$cat])) {
                $categories[$cat] = [];
            }
            $categories[$cat][] = $item;
        }

        $rowIndex = 6; // baris mulai checklist (untuk referensi styles)
        foreach ($categories as $catName => $checklists) {
            if (empty($checklists)) continue;

            $rows[] = [$catName]; // Subjudul
            $rows[] = ['Check Item', 'Description', 'Score', 'Comment', 'File'];

            foreach ($checklists as $item) {
                $rows[] = [
                    $item['check_item'] ?? '',
                    $item['description'] ?? '',
                    $item['score'] ?? '',
                    $item['comment'] ?? '',
                    $item['file'] ?? '',
                ];
            }

            $rows[] = ['']; // Spacer antar kategori
        }

        // Footer
        $rows[] = ['Final Score (%)', number_format($this->audit->final_score, 2)];
        $rows[] = ['General Comments', $this->audit->comments];

        return $rows;
    }

    public function headings(): array
    {
        return []; // Heading manual di array()
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Auto-size kolom
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Wrap text dan rata atas
                $sheet->getStyle("A1:E{$highestRow}")
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Border semua cell
                $sheet->getStyle("A1:E{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Style Subjudul Kategori
                for ($row = 5; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell("A{$row}")->getValue();
                    if (in_array($cellValue, ['Sort', 'Set In Order', 'Shine', 'Standardize', 'Sustain'])) {
                        $sheet->mergeCells("A{$row}:E{$row}");
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'DDEBF7'],
                            ],
                        ]);
                    }

                    // Heading baris kedua setelah kategori
                    if ($sheet->getCell("A{$row}")->getValue() === 'Check Item') {
                        $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'BDD7EE'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}
