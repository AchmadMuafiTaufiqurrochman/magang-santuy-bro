<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'id_package',
    ];

    // Relasi: Product milik satu Paket
    public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_package');
    }
}
