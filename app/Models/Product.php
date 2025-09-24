<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',       // dipakai untuk produk yang terhubung dengan package
        'id_package',  // foreign key ke Package
        'base_price',  // harga dasar untuk variasi paket
        'status',      // status produk (active/inactive)
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    /**
     * Relasi: Product milik satu Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'id_package');
    }

    /**
     * Relasi: Product punya banyak Paket
     */
    public function pakets(): HasMany
    {
        return $this->hasMany(Paket::class);
    }
}
