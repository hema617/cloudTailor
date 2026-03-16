<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'order_id',
        'tailor_id',
        'order_amount',
        'platform_commission',
        'tailor_earning'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }
}
