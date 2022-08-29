<?php

namespace App\Http\Requests\Auth;

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
            'smsCode' => 'required|min:4',
        ];
    }

    public function attributes(): array
    {
        return [
            'smsCode' => 'Код подтверждения'
        ];
    }
}
