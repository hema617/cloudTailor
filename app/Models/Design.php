<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }

    public function images()
    {
        return $this->hasMany(DesignImage::class);
    }

    public function options()
    {
        return $this->hasMany(DesignOption::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }
}
