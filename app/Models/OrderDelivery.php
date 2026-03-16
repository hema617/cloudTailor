<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    protected $fillable = [
        'order_id',
        'delivery_type',
        'courier_name',
        'tracking_number',
        'shipped_at',
        'delivered_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
