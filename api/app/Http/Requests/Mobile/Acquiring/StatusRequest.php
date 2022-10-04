<?php

namespace App\Http\Requests\Mobile\Acquiring;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'command' => 'required|string',
            "paymentMethodId" => 'required|string',
            "paymentId" => 'required|uuid'
        ];
    }
}
