<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailorHoliday extends Model
{
    protected $fillable = [
        'tailor_id',
        'date',
        'reason'
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }
}
