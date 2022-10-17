<?php

namespace App\Http\Requests\Mobile\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userIdentifier' => 'required|regex:/^7\d{10}$/',
            'otp' => 'required|min:4',
        ];
    }

    public function attributes(): array
    {
        return [
            'userIdentifier' => 'Телефон',
            'otp' => 'Код подтверждения'
        ];
    }
}
