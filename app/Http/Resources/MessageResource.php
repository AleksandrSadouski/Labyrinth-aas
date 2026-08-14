<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['name' => $this->player_name,
        'message' => $this->message,
        'created_at' => $this->created_at];
    }
}
