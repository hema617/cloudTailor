<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMeasurement extends Model
{
    protected $fillable = [
        'order_id',
        'measurement_name',
        'measurement_value'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
