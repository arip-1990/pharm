<?php

namespace App\Http\Requests\Mobile\Auth;

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
            'userIdentifier' => 'required|regex:/^7\d{10}$/',
            'password' => 'required|string|min:6|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'userIdentifier' => 'Телефон',
            'password' => 'Пароль'
        ];
    }
}
