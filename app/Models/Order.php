<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'technician_id',
        'order_date',
        'status',
        'total_price',
        'notes',
        'package_id',
    ];

    // Customer (alias user)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Service
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // Package
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    // Teknisi
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // Assignments
    public function orderAssignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class);
    }
}
