<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Model
{
    protected $fillable = ['name'];
    use HasApiTokens;

    public function player()
    {
        return $this->hasOne(Player::class, 'profile_id');
    }    
}