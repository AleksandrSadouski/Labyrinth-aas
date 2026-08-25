<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameHistory extends Model
{
    protected $fillable = ['profile_id', 'result', 'name_opponent', 'rating_opponent'];
    protected $casts = ['created_at' => 'datetime'];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }
}
