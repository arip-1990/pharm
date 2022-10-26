<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userIdentifier' => 'required|uuid',
            'name' => 'nullable|string',
            'phone' => 'nullable|regex:/^7\d{10}$/',
            'email' => 'nullable|email|max:100',
            'birthday' => 'nullable|date'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ФИО',
            'phone' => 'Телефон',
            'birthday' => 'День рождения',
        ];
    }
}
