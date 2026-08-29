<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['result' => $this->result,
        'name_opponent' => $this->name_opponent,
        'pvp_rating_opponent' => $this->pvp_rating_opponent,
        'pvp_rating' => $this->pvp_rating,
        'created_at' => $this->created_at];
    }
}
