<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'regex:/^((7\d{10})|([-.\w]+@([-\w]+\.)+[-\w]{2,8}$|^7\d{10}))$/'
            ],
            'password' => 'required|string|min:6'
        ];
    }
}
