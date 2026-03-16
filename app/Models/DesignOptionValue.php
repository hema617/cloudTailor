<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignOptionValue extends Model
{
    protected $fillable = [
        'design_option_id',
        'value',
        'price'
    ];

    public function option()
    {
        return $this->belongsTo(DesignOption::class,'design_option_id');
    }
}
