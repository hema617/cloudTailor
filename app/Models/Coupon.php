<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_usage',
        'used_count',
        'expires_at',
        'status'
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }
}
