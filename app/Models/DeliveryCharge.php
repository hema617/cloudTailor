<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    protected $fillable = [
        'city_id',
        'charge'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
