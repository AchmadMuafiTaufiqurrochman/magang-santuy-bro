<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'technician_id',
        'assigned_at',
        'assigned_by', // admin id
    ];

    // Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Technician (User)
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // Relasi ke Admin yang assign
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
