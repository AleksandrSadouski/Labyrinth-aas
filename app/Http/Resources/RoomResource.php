<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlayerResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['room_id' => $this->id,
        'room_type' => $this->room_type,
        'code' => $this->code,
        'maze' => $this->maze,
        'size' => $this->size,
        'branch_weight' => $this->branch_weight,
        'hallway_weight' => $this->hallway_weight,
        'entry_x' => $this->entry_x,
        'entry_y' => $this->entry_y,
        'exit_x' => $this->exit_x,
        'exit_y' => $this->exit_y,
        'current_turn' => $this->current_turn,
        'turn_total' => $this->turn_total,
        'status' => $this->status,
        'winner_order' => $this->winner_order,
        'draw' => $this->draw,
        'is_on_codeboard' => $this->is_on_codeboard,
        'players' => PlayerResource::collection($this->whenLoaded('players'))];
    }
}