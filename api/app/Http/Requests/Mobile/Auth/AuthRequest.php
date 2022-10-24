<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userIdentifier' => 'required|regex:/^7\d{10}$/',
            'fullName' => 'nullable|string',
            'birthday' => 'nullable|date',
            'email' => 'nullable|email|max:100',
            'gender' => 'nullable'
        ];
    }

    public function attributes(): array
    {
        return [
            'userIdentifier' => 'Телефон',
            'fullName' => 'ФИО',
            'birthday' => 'День рождения',
            'gender' => 'Пол'
        ];
    }
}
