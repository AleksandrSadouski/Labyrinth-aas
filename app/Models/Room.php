<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['code', 'maze', 'size', 'branch_weight', 'hallway_weight', 
    'entry_x', 'entry_y', 'exit_x', 'exit_y'];
    protected $casts = ['maze' => 'array'];

    public function players()
    {
        return $this->hasMany(Player::class, 'room_id');
    }    
}