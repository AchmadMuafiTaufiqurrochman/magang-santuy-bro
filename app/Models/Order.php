<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'date',
        'time_slot',
        'address',
        'note', // Akan menggunakan field note untuk menyimpan info products juga
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time_slot' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Method untuk mendapatkan selected products dari note
    public function getSelectedProductsAttribute()
    {
        if (str_contains($this->note, 'PRODUCTS:')) {
            $parts = explode('PRODUCTS:', $this->note);
            if (isset($parts[1])) {
                $productIds = json_decode(trim($parts[1]), true);
                return is_array($productIds) ? $productIds : [];
            }
        }
        return [];
    }

    // Method untuk mendapatkan products yang dipilih
    public function selectedProducts()
    {
        $productIds = $this->getSelectedProductsAttribute();
        return Product::whereIn('id', $productIds)->get();
    }

    // Method untuk mendapatkan note tanpa product data
    public function getCleanNoteAttribute()
    {
        if (str_contains($this->note, 'PRODUCTS:')) {
            $parts = explode('PRODUCTS:', $this->note);
            return trim($parts[0]);
        }
        return $this->note;
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}
