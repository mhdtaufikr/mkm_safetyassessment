<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaFinding extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'sa_findings';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'id_assessment',
        'countermeasure',
        'genba_date',
        'shop',
        'pic_area',
        'pic_repair',
        'due_date',
        'status',
        'progress_date',
        'checked',
        'code',
        'shop_id',
        'file',
    ];

    // Menonaktifkan created_at dan updated_at
    public $timestamps = false;

    // (Opsional) Relasi ke RiskAssessment jika diperlukan
    public function assessment()
    {
        return $this->belongsTo(RiskAssessment::class, 'id_assessment');
    }

    // (Opsional) Relasi ke Shop jika diperlukan
   public function shop()
{
    return $this->belongsTo(Shop::class, 'shop_id');
}

}