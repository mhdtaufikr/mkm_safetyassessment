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

        // Informasi Header
        $rows[] = ['Shop', $this->audit->shop];
        $rows[] = ['Date', \Carbon\Carbon::parse($this->audit->date)->format('d M Y')];
        $rows[] = ['Auditor', $this->audit->auditor];
        $rows[] = ['']; // Spasi kosong
        $rows[] = ['Checklist']; // Judul Checklist
        $rows[] = ['Check Item', 'Description', 'Score', 'Comment', 'File'];

        // Isi checklist
        foreach ($this->audit->scores as $item) {
            $rows[] = [
                $item['check_item'] ?? '',
                $item['description'] ?? '',
                $item['score'] ?? '',
                $item['comment'] ?? '',
                $item['file'] ?? '',
            ];
        }

        // Spasi & Footer
        $rows[] = [''];
        $rows[] = ['Final Score (%)', number_format($this->audit->final_score, 2)];
        $rows[] = ['General Comments', $this->audit->comments];

        return $rows;
    }

    public function headings(): array
    {
        return []; // Kosong karena heading sudah disusun manual di array()
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => [ // Heading tabel checklist
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9E1F2'] // biru muda
                ],
            ],
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

                // Auto-size semua kolom A - E
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Wrap text dan rata atas
                $sheet->getStyle("A1:E{$highestRow}")
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                // Tinggi baris
                for ($i = 1; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(24);
                }

                // Border semua cell
                $sheet->getStyle("A1:E{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));

                // Bold dan warna latar untuk "Checklist" title
                $sheet->mergeCells('A5:E5');
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4B084'], // peach
                    ],
                ]);

                // Bold final score & comments
                $sheet->getStyle("A" . ($highestRow - 1))->getFont()->setBold(true);
                $sheet->getStyle("A" . ($highestRow))->getFont()->setBold(true);
            },
        ];
    }
}
