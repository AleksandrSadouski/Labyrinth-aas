<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['player_order', 'x', 'y'];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'player_id');
    }
}