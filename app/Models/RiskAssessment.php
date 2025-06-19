<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskAssessment extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'risk_assessment_headers';

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'shop_id',
        'scope_number',
        'finding_problem',
        'potential_hazards',
        'accessor',
        'severity',
        'possibility',
        'score',
        'risk_level',
        'risk_reduction_proposal',
        'file',
        'created_by',
    ];

    /**
     * Relasi ke model Shop
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Relasi ke model User sebagai creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke RiskAssessmentDetail (jika digunakan)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detail()
    {
        return $this->hasMany(RiskAssessmentDetail::class, 'risk_assessment_header_id');
    }

    /**
     * Relasi ke SaFinding (1 assessment -> 1 finding)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function finding()
    {
        return $this->hasOne(SaFinding::class, 'id_assessment', 'id');
    }
}
