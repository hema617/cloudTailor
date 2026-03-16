<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailorService extends Model
{
    protected $fillable = [
        'tailor_id',
        'name',
        'price'
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }
}
