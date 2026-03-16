<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailorPortfolio extends Model
{
    protected $fillable = [
        'tailor_id',
        'image',
        'title'
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }
}