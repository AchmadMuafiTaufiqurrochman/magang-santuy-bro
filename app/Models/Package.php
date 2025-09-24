<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    // Relasi: 1 Paket punya banyak Produk
    public function products()
    {
        return $this->hasMany(Product::class, 'id_package');
    }
}
