<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'image',
        'status'
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }
}
