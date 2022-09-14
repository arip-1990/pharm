<?php

namespace App\Http\Requests\Order;

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
            'price' => 'required|numeric',
            'store' => 'required|uuid',
            'city' => 'required_if:delivery,1',
            'street' => 'required_if:delivery,1',
            'house' => 'required_if:delivery,1',
            'entrance' => 'nullable|number',
            'floor' => 'nullable|number',
            'apt' => 'nullable|number',
            'service_to_door' => 'boolean',
            'rule' => 'accepted',
            'items' => 'array',
            'items.*.id' => 'required|uuid',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
        ];
    }
}
