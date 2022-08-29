<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'oldPassword' => 'required|min:6|max:50',
            'password' => 'required|confirmed|min:6|max:50',
        ];
    }

    public function attributes(): array
    {
        return [
            'oldPassword' => 'Старый пароль',
            'password' => 'Новый пароль',
        ];
    }
}
