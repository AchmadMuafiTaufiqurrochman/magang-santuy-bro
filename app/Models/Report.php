<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'title',
        'description',
        'type',
        'report_date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
