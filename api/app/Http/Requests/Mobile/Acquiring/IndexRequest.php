<?php

namespace App\Http\Requests\Mobile\Acquiring;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            "orderUuid" => 'required|uuid',
            "orderId" => 'required|string',
            "returnUrl" => 'nullable|string'
        ];
    }
}
