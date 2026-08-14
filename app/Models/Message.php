<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message'];
    protected $casts = ['created_at' => 'datetime'];

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function getPlayerNameAttribute(): string
    {
        return $this->player->profile->name;
    }
}
