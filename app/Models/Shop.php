<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';

    // Tambahkan kolom 'image' agar bisa disimpan
    protected $fillable = ['name', 'image'];

    // Akses gambar dengan path lengkap (misalnya untuk di view)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('default-shop.png');
    }
}
