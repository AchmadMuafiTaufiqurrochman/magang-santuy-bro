<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'order_id',
        'technician_id',
        'scheduled_date',
        'scheduled_time',
        'status',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
