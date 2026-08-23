<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['old_password' => 'required|string|min:8|max:32',
        'new_password' => 'required|string|min:8|max:32|confirmed'];
    }
}
