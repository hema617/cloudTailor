<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignImage extends Model
{
    protected $fillable = [
        'design_id',
        'image'
    ];

    public function design()
    {
        return $this->belongsTo(Design::class);
    }
}
