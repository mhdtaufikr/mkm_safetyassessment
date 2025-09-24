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

    public function __construct()
    {
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
            'Assessment ID','Shop','Scope','Problem','Hazard','Accessor','Severity','Probability','Score','Risk Level',
            'Reduction Measures','Follow-up Status','Created At','Countermeasure','Genba Date','PIC Area','PIC Repair',
            'Due Date','Status','Progress Date','Checked','Code','File Before','File After',
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
        return [1 => ['font' => ['bold' => true]]];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

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
                // Lebar kolom W & X (gambar) – 50 ~ 55 biasanya pas untuk 220px
                $sheet->getColumnDimension('W')->setWidth(52);
                $sheet->getColumnDimension('X')->setWidth(52);

                // Border & alignment
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                      ->getBorders()->getAllBorders()
                      ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                      ->getAlignment()->setWrapText(true)->setVertical('top')->setHorizontal('left');

                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal('center');

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
                            $fixedAfter = $this->normalizedImagePath($pathAfter); // perbaiki orientasi
                            $h = $this->placeImage($sheet, $fixedAfter, 'X', $row, $this->imgMaxWidthPx);
                            $maxImageHeightPx = max($maxImageHeightPx, $h);
                        }
                    }

                    // Konversi px -> point (approx 0.75 pt per px). Tambah padding.
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
