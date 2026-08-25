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
        'rating_opponent' => $this->rating_opponent,
        'rating' => $this->rating,
        'created_at' => $this->created_at];
    }
}
