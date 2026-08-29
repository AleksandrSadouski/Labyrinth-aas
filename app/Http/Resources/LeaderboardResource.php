<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProfileResource;

class LeaderboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
        'name' => $this->name,
        'pvp_rating' => $this->when(!empty($this->pvp_rating), $this->pvp_rating),
        'sp_game_completed' => $this->when(!empty($this->sp_game_completed), $this->sp_game_completed)];
    }
}