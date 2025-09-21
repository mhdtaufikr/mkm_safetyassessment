<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saudit extends Model
{
    use HasFactory;

    protected $table = 'saudits';

    protected $fillable = [
        'shop',
        'date',
        'auditor',
        'scores',
        'final_score',
        'comments',
        'files',
    ];

    protected $casts = [
        'scores' => 'array',
        'files' => 'array',
        'date' => 'date',
        'final_score' => 'float',
    ];
}
