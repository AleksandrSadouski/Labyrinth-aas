<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MessageResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['player_id' => $this->id,
        'room_id' => $this->room_id,
        'profile_id' => $this->profile_id,
        'player_order' => $this->player_order,
        'x' => $this->x,
        'y' => $this->y,
        'finished' => $this->finished,
        'messages' => MessageResource::collection($this->whenLoaded('messages'))];
    }
}
