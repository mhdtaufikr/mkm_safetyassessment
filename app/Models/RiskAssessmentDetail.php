<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskAssessmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_assessment_header_id',
        'scope',
        'finding_problem',
        'potential_hazard',
        'severity',
        'possibility',
        'score',
        'risk_level',
        'reduction_measures',
    ];

    /**
     * Relasi ke tabel risk_assessment_headers
     */
    public function header()
{
    return $this->belongsTo(RiskAssessment::class, 'risk_assessment_header_id'); // ✅ BENAR
}


    /**
     * Relasi ke tabel risk_actions (tindakan temuan)
     */
    public function actions()
    {
        return $this->hasMany(SaFinding::class, 'risk_assessment_detail_id');
    }
}
