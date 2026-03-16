<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TailorAvailability extends Model
{
    protected $fillable = [
        'tailor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available'
    ];

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }
}
