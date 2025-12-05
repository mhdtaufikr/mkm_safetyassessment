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
use PhpOffice\PhpSpreadsheet\Style\Color;

class RiskAssessmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $data;
    protected $start_date;
    protected $end_date;

    // Lebarin default kolom gambar & tinggi baris
    private int $imgMaxWidthPx = 220;   // lebar gambar (px) di kolom W/X
    private int $rowMinHeightPt = 150;  // tinggi baris minimal (points)

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
        $this->data = RiskAssessment::with(['shop', 'finding'])
            ->whereMonth('created_at', 11)
            ->whereYear('created_at', Carbon::now()->year)
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
            'Assessment ID','Shop','Scope','Problem','Hazard','Accessor','Severity','Probability','Score','Risk Level',
            'Reduction Measures','Follow-up Status','Created At','Countermeasure','Genba Date','PIC Area','PIC Repair',
            'Due Date','Status','Progress Date','Checked','Code','File Before','File After',
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
        return [1 => ['font' => ['bold' => true]]];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet   = $event->sheet->getDelegate();
            $parent  = $sheet->getParent(); // workbook
            $highestRow    = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            // === Default: Font Ebrima 12pt untuk seluruh workbook ===
            $parent->getDefaultStyle()->getFont()
                ->setName('Ebrima')
                ->setSize(18);

            // Landscape & fit
            $sheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                ->setFitToWidth(1)->setFitToHeight(0);

            // Margin
            $sheet->getPageMargins()->setTop(0.3)->setBottom(0.3)->setLeft(0.4)->setRight(0.4);

            // Freeze header
            $sheet->freezePane('A2');

            // Lebarkan kolom gambar + autosize kolom lain
            foreach (range('A', 'V') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $sheet->getColumnDimension('W')->setWidth(52); // file before
            $sheet->getColumnDimension('X')->setWidth(52); // file after

            // Border
            $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                  ->getBorders()->getAllBorders()
                  ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Alignment umum
            $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                  ->getAlignment()
                  ->setWrapText(true)
                  ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
                  ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            // Header: Ebrima 14pt bold + center
            $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                'font' => [
                    'name' => 'Ebrima',
                    'bold' => true,
                    'size' => 22,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            // ====== Sisipkan gambar + atur tinggi baris dinamis ======
            $row = 2;

            foreach ($this->data as $item) {
                $finding = $item->finding;
                $maxImageHeightPx = 0;

                // BEFORE
                if ($item->file) {
                    $pathBefore = storage_path('app/public/risk_files/' . basename($item->file));
                    if (is_file($pathBefore) && @exif_imagetype($pathBefore)) {
                        $fixedBefore = $this->normalizedImagePath($pathBefore); // perbaiki orientasi
                        $h = $this->placeImage($sheet, $fixedBefore, 'W', $row, $this->imgMaxWidthPx);
                        $maxImageHeightPx = max($maxImageHeightPx, $h);
                    }
                }

                // AFTER
                if ($finding && $finding->file) {
                    $pathAfter = storage_path('app/public/sa_findings_file/' . basename($finding->file));
                    if (is_file($pathAfter) && @exif_imagetype($pathAfter)) {
                        $fixedAfter = $this->normalizedImagePath($pathAfter);
                        $h = $this->placeImage($sheet, $fixedAfter, 'X', $row, $this->imgMaxWidthPx);
                        $maxImageHeightPx = max($maxImageHeightPx, $h);
                    }
                }

                // px -> point (~0.75 pt per px) + padding
                $rowHeightPt = max($this->rowMinHeightPt, (int)round($maxImageHeightPx * 0.75) + 20);
                $sheet->getRowDimension($row)->setRowHeight($rowHeightPt);

                $row++;
            }
        },
    ];
}


    /**
     * Tempel gambar pada cell (kolom/row) dengan lebar maksimum $maxWidthPx,
     * menjaga aspek rasio. Return: tinggi gambar (px) sesudah resize.
     */
    private function placeImage(Worksheet $sheet, string $imgPath, string $column, int $row, int $maxWidthPx): int
    {
        [$w, $h] = @getimagesize($imgPath) ?: [0, 0];
        if ($w <= 0 || $h <= 0) return 0;

        // Skala proporsional ke max width
        if ($w > $maxWidthPx) {
            $scale = $maxWidthPx / $w;
            $targetW = (int)round($w * $scale);
            $targetH = (int)round($h * $scale);
        } else {
            $targetW = $w;
            $targetH = $h;
        }

        $drawing = new Drawing();
        $drawing->setName('Photo');
        $drawing->setDescription('Photo');
        $drawing->setPath($imgPath);
        $drawing->setWidth($targetW);             // set lebar
        // Tinggi akan mengikuti proporsi dari setWidth()
        $drawing->setCoordinates($column . $row);
        $drawing->setOffsetX(6);                  // padding kiri
        $drawing->setOffsetY(6);                  // padding atas
        $drawing->setWorksheet($sheet);

        return $targetH;
    }

    /**
     * Perbaiki orientasi gambar berdasarkan EXIF Orientation (HP/Android/iPhone).
     * Simpan hasil koreksi sementara di storage/app/temp_export/.
     */
    private function normalizedImagePath(string $path): string
    {
        // Jika ext tidak dikenal/EXIF tidak ada, pakai original
        if (!function_exists('exif_read_data')) return $path;

        try {
            $exif = @exif_read_data($path);
            $orientation = $exif['Orientation'] ?? 1;

            if (!in_array($orientation, [3, 6, 8], true)) {
                return $path; // sudah benar
            }

            // Load GD image
            $img = @imagecreatefromstring(file_get_contents($path));
            if (!$img) return $path;

            switch ($orientation) {
                case 3: $img = imagerotate($img, 180, 0); break;   // upside down
                case 6: $img = imagerotate($img, -90, 0); break;   // 90 CW
                case 8: $img = imagerotate($img, 90, 0); break;    // 90 CCW
            }

            // Simpan ke file sementara (cache)
            $tmpDir = storage_path('app/temp_export');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $tmp = $tmpDir . '/' . md5($path . filemtime($path)) . '.' . ($ext ?: 'jpg');

            // Simpan sesuai tipe
            if (in_array($ext, ['png'], true)) {
                imagepng($img, $tmp);
            } else {
                imagejpeg($img, $tmp, 90);
            }

            imagedestroy($img);
            return $tmp;
        } catch (\Throwable $e) {
            // Kalau gagal normalisasi, tetap pakai path asli
            return $path;
        }
    }
}
