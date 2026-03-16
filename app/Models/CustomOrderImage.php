<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOrderImage extends Model
{
    protected $fillable = [
        'custom_order_id',
        'image'
    ];

    public function customOrder()
    {
        return $this->belongsTo(CustomOrder::class);
    }
}
