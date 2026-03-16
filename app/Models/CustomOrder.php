<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    protected $fillable = [
        'user_id',
        'tailor_id',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }

    public function images()
    {
        return $this->hasMany(CustomOrderImage::class);
    }
}
