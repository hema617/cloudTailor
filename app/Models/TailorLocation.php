<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailorLocation extends Model
{
    protected $fillable = [
        'tailor_id',
        'city_id',
        'address',
        'latitude',
        'longitude'
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
