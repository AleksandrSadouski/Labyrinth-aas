<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['size' => 'required|integer|min:10|max:1000',
        'branch_weight' => 'required|float|min:0|max:1',
        'hallway_weight' => 'required|float|min:0|max:1'];
    }
}