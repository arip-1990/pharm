<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName' => 'required|max:50',
            'email' => 'nullable|email|max:100|unique:users',
            'phone' => 'required|unique:users|regex:/^7\d{10}$/',
            'birthDate' => 'required|date',
            'gender' => 'required|digits_between:0,2',
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
            'lastName' => 'Фамилия',
            'middleName' => 'Отчество'
        ];
    }
}
