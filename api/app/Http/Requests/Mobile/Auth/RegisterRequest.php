<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userIdentifier' => 'required|regex:/^7\d{10}$/',
            'fullName' => 'required|string',
            'birthday' => 'required|date',
            'password' => 'required|string|min:6|max:50',
            'email' => 'nullable|email|max:100',
            'gender' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'userIdentifier' => 'Телефон',
            'fullName' => 'ФИО',
            'birthDate' => 'День рождения',
            'gender' => 'Пол',
            'password' => 'Пароль',
        ];
    }
}
