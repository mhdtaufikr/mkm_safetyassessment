<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskAssessment extends Model
{
    use HasFactory;

    protected $table = 'risk_assessment_headers';

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
     * Relasi ke model Shop.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Relasi ke model User (jika ingin menampilkan siapa yang create).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detail()
    {
    return $this->hasMany(RiskAssessmentDetail::class, 'risk_assessment_header_id');
    }

}
