<?php

namespace App\Http\Requests\Order;

use App\Models\Status\Mobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device.platform' => 'required|string',
            'orders.*.uuid' => 'required|uuid',
            'orders.*.groupId' => 'nullable|uuid',
            'orders.*.status' => [new Enum(Mobile::class)],
            'orders.*.name' => 'required|string',
            'orders.*.phone' => 'required|string',
            'orders.*.email' => 'required|email',
            'orders.*.city' => 'required|string',
            'orders.*.address' => 'required|string',
            'orders.*.price' => 'required|numeric',
            'orders.*.deliveryPrice' => 'required|numeric',
            'orders.*.authorizedBonuses' => 'required|numeric',
            'orders.*.promocode' => 'nullable|string',
            'orders.*.appliedDiscount' => 'required|numeric',
            'orders.*.loyaltyCard' => 'nullable|string',
            'orders.*.delivery' => 'required|string',
            'orders.*.deliveryName' => 'required|string',
            'orders.*.payment' => 'required|string',
            'orders.*.paymentName' => 'required|string',
            'orders.*.paymentProcessed' => 'required|boolean',
            'orders.*.paymentId' => 'required|uuid',
            'orders.*.paymentGateway' => 'required|string',
            'orders.*.deliveryComment' => 'required|string',
            'orders.*.addressData' => 'nullable|array',
            'orders.*.addressData.city' => 'required|string',
            'orders.*.addressData.region' => 'required|string',
            'orders.*.addressData.street' => 'required|string',
            'orders.*.addressData.house' => 'required|string',
            'orders.*.addressData.apt' => 'nullable|string',
            'orders.*.items.*.id' => 'required|uuid',
            'orders.*.items.*.configurationId' => 'required|string',
            'orders.*.items.*.privateId' => 'required|string',
            'orders.*.items.*.name' => 'required|string',
            'orders.*.items.*.price' => 'required|numeric',
            'orders.*.items.*.quantity' => 'required|numeric',
            'orders.*.items.*.discount' => 'required|numeric',
            'orders.*.items.*.subtotal' => 'required|numeric',

//            'delivery' => 'required|digits_between:0,1',
//            'payment' => 'required|digits_between:0,1',
//            'store' => 'required|uuid',
//            'rule' => 'required',
//            'city' => 'required_if:delivery,1',
//            'street' => 'required_if:delivery,1',
//            'house' => 'required_if:delivery,1',
//            'entrance' => 'nullable|number',
//            'floor' => 'nullable|number',
//            'apartment' => 'nullable|number',
//            'service_to_door' => 'boolean',
        ];
    }
}
