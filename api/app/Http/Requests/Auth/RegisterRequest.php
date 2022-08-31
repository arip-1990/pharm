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
            'firstName' => 'required|max:50',
            'email' => 'required|email|max:100',
            'phone' => 'required|regex:/^7\d{10}$/',
            'birthDate' => 'required|date',
            'gender' => 'required|digits_between:0,2',
            'password' => 'required|min:6|max:50',
//            'agreeToTerms' => 'required|boolean',
            'cardNumber' => 'nullable|max:20',
            'lastName' => 'nullable|max:50',
            'middleName' => 'nullable|max:50'
        ];
    }

    public function attributes(): array
    {
        return [
            'firstName' => 'Имя',
            'phone' => 'Телефон',
            'birthDate' => 'День рождения',
            'gender' => 'Пол',
            'password' => 'Пароль',
            'cardNumber' => 'Номер карты',
            'lastName' => 'Фамилия',
            'middleName' => 'Отчество'
        ];
    }
}
