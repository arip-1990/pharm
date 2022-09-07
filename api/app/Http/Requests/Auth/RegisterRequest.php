<?php

namespace App\Http\Requests\Auth;

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
            'fullName' => 'required|string',
            'email' => 'nullable|email|max:100',
            'phone' => 'required|regex:/^7\d{10}$/',
            'birthDate' => 'required|date',
            'gender' => 'required|digits_between:0,2',
            'password' => 'required|string|min:6|max:50',
//            'agreeToTerms' => 'required|boolean',
            'cardNumber' => 'nullable|string|max:20',
        ];
    }

    public function attributes(): array
    {
        return [
            'fullName' => 'ФИО',
            'phone' => 'Телефон',
            'birthDate' => 'День рождения',
            'gender' => 'Пол',
            'password' => 'Пароль',
            'cardNumber' => 'Номер карты'
        ];
    }
}
