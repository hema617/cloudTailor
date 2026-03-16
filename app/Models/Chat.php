<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'tailor_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tailor()
    {
        return $this->belongsTo(Tailor::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
