<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JoinRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = ['room_type' => 'required|string|in:pvplocal,pvppublic'];
        
        if ($this->input('room_type') == 'pvplocal') 
            {
                $rules['code'] = 'required|string|size:6';
            }
        
        return $rules;
    }
}
