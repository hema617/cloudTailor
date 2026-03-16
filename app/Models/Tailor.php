<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tailor extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'description',
        'experience_years',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->hasOne(TailorLocation::class);
    }

    public function services()
    {
        return $this->hasMany(TailorService::class);
    }

    public function portfolios()
    {
        return $this->hasMany(TailorPortfolio::class);
    }

    public function availability()
    {
        return $this->hasMany(TailorAvailability::class);
    }

    public function holidays()
    {
        return $this->hasMany(TailorHoliday::class);
    }
}
