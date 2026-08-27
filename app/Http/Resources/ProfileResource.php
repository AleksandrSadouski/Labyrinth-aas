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
        'rating' => $this->rating,
        'game_total' => $this->game_total,
        'win_total' => $this->win_total,
        'draw_total' => $this->draw_total,
        'lose_total' => $this->lose_total,
        'sp_game_completed' => $this->sp_game_completed,
        'player' => new PlayerResource($this->whenLoaded('player')),
        'game_history' => GameHistoryResource::collection($this->whenLoaded('gameHistories'))];
    }
}