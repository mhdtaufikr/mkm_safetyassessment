<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaFinding extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'sa_findings';

    // Kolom yang dapat diisi secara massal
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

    // Menonaktifkan timestamps
    public $timestamps = false;

    /**
     * Relasi ke RiskAssessment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assessment()
    {
        return $this->belongsTo(RiskAssessment::class, 'id_assessment');
    }

    /**
     * Relasi ke Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
