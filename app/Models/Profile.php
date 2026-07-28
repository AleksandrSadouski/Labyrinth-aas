<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name'];

    public function player()
    {
        return $this->hasOne(Player::class, 'profile_id');
    }    
}