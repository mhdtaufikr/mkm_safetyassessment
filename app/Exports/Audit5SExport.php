<?php

namespace App\Exports;

use App\Models\Saudit;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class Audit5SExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $audits;
    protected $highlightRows = [];
    protected $imageRows = [];

    public function __construct()
    {
        // Mengambil data dalam seminggu terakhir dan mengurutkannya dari yang terbaru
        $this->audits = Saudit::where('created_at', '>=', Carbon::now()->subWeek())
                            ->latest()
                            ->get();
    }

    public function array(): array
    {
        $rows = [];
        $rowCounter = 1;

        foreach ($this->audits as $index => $audit) {
            $info = [
                ['No.', $index + 1],
                ['Date', \Carbon\Carbon::parse($audit->date)->format('d M Y')],
                ['Shop', $audit->shop],
                ['Auditor', $audit->auditor],
                ['Final Score', number_format($audit->final_score, 2) . '%'],
                ['General Comments', $audit->comments],
                [''],
                ['Category', 'Check Item', 'Description', 'Score', 'Comment', 'File']
            ];

            foreach ($info as $line) {
                $rows[] = $line;
                $this->highlightRows[] = $rowCounter++;
            }

            $categoryMap = [
                0 => 'Sort', 1 => 'Sort', 2 => 'Sort', 3 => 'Sort', 4 => 'Sort', 5 => 'Sort',
                6 => 'Set In Order', 7 => 'Set In Order', 8 => 'Set In Order', 9 => 'Set In Order', 10 => 'Set In Order',
                11 => 'Shine', 12 => 'Shine', 13 => 'Shine', 14 => 'Shine', 15 => 'Shine',
                16 => 'Standardize', 17 => 'Standardize', 18 => 'Standardize', 19 => 'Standardize', 20 => 'Standardize',
                21 => 'Sustain', 22 => 'Sustain', 23 => 'Sustain', 24 => 'Sustain', 25 => 'Sustain',
            ];

            // Pastikan 'scores' adalah array sebelum di-loop
            $scores = is_array($audit->scores) ? $audit->scores : json_decode($audit->scores, true) ?? [];

            foreach ($scores as $i => $item) {
                $cat = $categoryMap[$i] ?? 'Uncategorized';

                $rows[] = [
                    $cat,
                    $item['check_item'] ?? '',
                    $item['description'] ?? '',
                    $item['score'] ?? '',
                    $item['comment'] ?? '',
                    isset($item['file']) ? 'Foto' : '',
                ];

                if (!empty($item['file'])) {
                    $this->imageRows[] = [
                        'row' => $rowCounter,
                        'path' => storage_path('app/public/saudit_files/' . basename($item['file'])),
                    ];
                }

                $rowCounter++;
            }

            $rows[] = [''];
            $rowCounter++;
            $rows[] = ['──────────────────────────────────────────────────────'];
            $rowCounter++;
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        foreach ($this->highlightRows as $row) {
            $styles[$row] = ['font' => ['bold' => true, 'color' => ['rgb' => '1F4E78']]];
        }
        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Orientasi dan margin
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageMargins()->setTop(0.25)->setBottom(0.25)->setLeft(0.25)->setRight(0.25);
                $sheet->getSheetView()->setZoomScale(100);

                // Kolom auto size A-E, kolom F untuk gambar diatur manual
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getColumnDimension('F')->setWidth(40); // untuk gambar landscape

                // Wrap text, border
                $sheet->getStyle("A1:F{$highestRow}")
                    ->getAlignment()->setWrapText(true)->setVertical('top');

                $sheet->getStyle("A1:F{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle('thin');

                // Header checklist
                for ($row = 1; $row <= $highestRow; $row++) {
                    $val = $sheet->getCell("A{$row}")->getValue();
                    if ($val === 'Category') {
                        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => 'solid',
                                'startColor' => ['rgb' => 'DDEBF7'],
                            ],
                        ]);
                    }

                    if (str_contains($val, '────')) {
                        $sheet->mergeCells("A{$row}:F{$row}");
                        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('AAAAAA');
                    }
                }

                // Tambah gambar ke kolom F (landscape mode)
                foreach ($this->imageRows as $img) {
                    if (file_exists($img['path']) && exif_imagetype($img['path'])) {
                        $drawing = new Drawing();
                        $drawing->setPath($img['path']);
                        $drawing->setWidth(160); // Mengatur gambar menyamping
                        $drawing->setCoordinates('F' . $img['row']);
                        $drawing->setOffsetX(5);
                        $drawing->setWorksheet($sheet);

                        // Atur tinggi baris agar gambar terlihat proporsional
                        $sheet->getRowDimension($img['row'])->setRowHeight(85);
                    }
                }
            },
        ];
    }
}