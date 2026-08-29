<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\GameHistoryResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id_profile' => $this->id,
        'name' => $this->name,
        'pvp_rating' => $this->pvp_rating,
        'sp_rating' => $this->sp_rating,
        'pvp_game_total' => $this->game_total,
        'pvp_win_total' => $this->win_total,
        'pvp_draw_total' => $this->draw_total,
        'pvp_lose_total' => $this->lose_total,
        'sp_game_completed' => $this->sp_game_completed,
        'player' => new PlayerResource($this->whenLoaded('player')),
        'game_history' => GameHistoryResource::collection($this->whenLoaded('gameHistories'))];
    }
}