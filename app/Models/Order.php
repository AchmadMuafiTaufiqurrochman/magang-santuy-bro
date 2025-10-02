<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

<<<<<<< HEAD
    public function service()
=======
    // Alias customer untuk user (karena user adalah customer yang order)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke technician melalui order assignments
    public function technician(): BelongsTo
    {
        // Ambil technician dari order assignment pertama
        $assignment = $this->orderAssignments()->first();
        return $assignment ? $assignment->technician() : $this->belongsTo(User::class, 'user_id')->whereNull('id');
    }

    public function package(): BelongsTo
>>>>>>> d74af388c4b535315f1ec848e10a72ca08f85ea4
    {
        return $this->belongsTo(Service::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
