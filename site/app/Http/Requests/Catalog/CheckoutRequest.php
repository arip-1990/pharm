<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery' => 'required|digits_between:0,1',
            'payment' => 'required|digits_between:0,1',
            'store' => 'required|uuid',
            'rule' => 'required',
            'city' => 'required_if:delivery,1',
            'street' => 'required_if:delivery,1',
            'house' => 'required_if:delivery,1',
            'entrance' => 'nullable|numeric',
            'floor' => 'nullable|numeric',
            'apartment' => 'nullable|numeric',
            'service_to_door' => 'boolean',
        ];
    }
}
