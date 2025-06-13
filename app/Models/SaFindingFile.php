<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaFindingFile extends Model
{
    use HasFactory;

    protected $table = 'sa_finding_files';

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

    public $timestamps = true;

    public function saFinding()
    {
        return $this->belongsTo(SaFinding::class);
    }
}
