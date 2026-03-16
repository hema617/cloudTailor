<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignOption extends Model
{
    protected $fillable = [
        'design_id',
        'name'
    ];

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function values()
    {
        return $this->hasMany(DesignOptionValue::class);
    }
}
