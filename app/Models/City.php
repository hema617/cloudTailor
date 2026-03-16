<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'state',
        'country',
        'status'
    ];

    public function deliveryCharges()
    {
        return $this->hasMany(DeliveryCharge::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
