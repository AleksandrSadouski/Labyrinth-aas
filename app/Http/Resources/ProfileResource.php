<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id_profile' => $this->id,
        'name' => $this->name,
        'rating' => $this->rating,
        'win_total' => $this->win_total,
        'draw_total' => $this->draw_total,
        'lose_total' => $this->lose_total,
        'player' => new PlayerResource($this->whenLoaded('player'))];
    }
}