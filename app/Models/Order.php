<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'technician_id',
        'service_id',
        'product_id',
        'package_id',
        'service_date',
        'status',
        'time_slot',
        'address',

        'note',
        'total_price',
        'technician_notes',
        'completion_photo',
        'completion_notes',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'service_date' => 'date',
        'time_slot' => 'datetime:H:i',
        'completed_at' => 'datetime',
    ];

    // Customer
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Technician
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function selectedProducts()
    {
        // Ambil data produk dari field note yang berisi JSON
        $note = $this->note ?? '';

        // Cari pattern untuk products yang disimpan dalam note
        if (preg_match('/Products: \[(.*?)\]/', $note, $matches)) {
            $productIds = array_map('trim', explode(',', $matches[1]));
            return Product::whereIn('id', $productIds)->get();
        }

        return collect([]); // Return empty collection jika tidak ada produk
    }

    // Relasi ke order assignments
    public function orderAssignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class);
    }

    // Relasi ke transaction
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id');
    }

    // Boot method untuk hitung total_price otomatis
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            $total = 0;

            if ($order->package_id) {
                $total += Package::whereKey($order->package_id)->value('price') ?? 0;
            }

            if ($order->service_id) {
                $total += Service::whereKey($order->service_id)->value('price') ?? 0;
            }

            if ($order->product_id) {
                $total += Product::whereKey($order->product_id)->value('price') ?? 0;
            }

            $order->total_price = $total;
        });
    }
}
