<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CodeboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['code' => $this->code,
        'size' => $this->size,
        'branch_weight' => $this->branch_weight,
        'hallway_weight' => $this->hallway_weight,
        ];
    }
}
