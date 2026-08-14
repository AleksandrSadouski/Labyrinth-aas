<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RoomStatus;

class Room extends Model
{
    public const MIN_PLAYERS = 1;
    public const MAX_PLAYERS = 2;

    protected $fillable = ['code', 'maze', 'size', 'branch_weight', 'hallway_weight', 
    'entry_x', 'entry_y', 'exit_x', 'exit_y'];

    protected $casts = ['maze' => 'array',
    'status' => RoomStatus::class];

    public function players()
    {
        return $this->hasMany(Player::class, 'room_id');
    }    
}