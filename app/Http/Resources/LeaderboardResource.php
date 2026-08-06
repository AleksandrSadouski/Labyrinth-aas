<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
        'name' => $this->name,
        'max_rating' => $this->rating];
    }
}