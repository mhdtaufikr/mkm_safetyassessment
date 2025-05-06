<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetySchedule extends Model
{
    use HasFactory;

    public function shop()
{
    return $this->belongsTo(MasterShop::class, 'shop_id');
}

}
