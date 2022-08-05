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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|unique:users|regex:/^7\d{10}$/',
            'birthDate' => 'required|date',
            'gender' => 'required|digits_between:0,2',
            'password' => 'required|string|min:6|max:50',
            'cardNumber' => 'nullable|string|max:20',
            'lastName' => 'nullable|string|max:50',
            'middleName' => 'nullable|string|max:50'
        ];
    }
}
