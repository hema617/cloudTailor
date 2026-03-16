<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'tailor_id',
        'total_amount',
        'status',
        'delivery_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function measurements()
    {
        return $this->hasMany(OrderMeasurement::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function attachments()
    {
        return $this->hasMany(OrderAttachment::class);
    }

    public function delivery()
    {
        return $this->hasOne(OrderDelivery::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
