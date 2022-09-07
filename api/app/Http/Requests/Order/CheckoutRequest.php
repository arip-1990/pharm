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
            'device.platform' => 'nullable|string',
            'orders.*.uuid' => 'nullable|uuid',
            'orders.*.groupId' => 'nullable|uuid',
            'orders.*.status' => ['nullable', new Enum(Mobile::class)],
            'orders.*.name' => 'nullable|string',
            'orders.*.phone' => 'nullable|string|regex:/^7\d{10}$/',
            'orders.*.email' => 'nullable|email',
            'orders.*.price' => 'nullable|numeric',
            'orders.*.deliveryPrice' => 'nullable|numeric',
            'orders.*.authorizedBonuses' => 'nullable|numeric',
            'orders.*.promocode' => 'nullable|string',
            'orders.*.appliedDiscount' => 'nullable|numeric',
            'orders.*.loyaltyCard' => 'nullable|string',
            'orders.*.hasPreorderItems' => 'nullable|boolean',
            'orders.*.createdOn' => 'nullable|date',
            'orders.*.updatedOn' => 'nullable|date',

            'orders.*.delivery' => 'required|string',
            'orders.*.deliveryName' => 'required|string',
            'orders.*.deliveryComment' => 'required|string',

            'orders.*.payment' => 'required|string',
            'orders.*.paymentName' => 'required|string',
            'orders.*.paymentProcessed' => 'required|boolean',
            'orders.*.paymentId' => 'required|uuid',
            'orders.*.paymentGateway' => 'required|string',

            'orders.*.city' => 'required|string',
            'orders.*.address' => 'required|string',
            'orders.*.addressData' => 'nullable|array',
            'orders.*.addressData.city' => 'required|string',
            'orders.*.addressData.region' => 'required|string',
            'orders.*.addressData.street' => 'required|string',
            'orders.*.addressData.house' => 'required|string',
            'orders.*.addressData.apt' => 'nullable|string',

            'orders.*.items.*.id' => 'required|uuid',
            'orders.*.items.*.privateId' => 'required|string',
            'orders.*.items.*.configurationId' => 'required|string',
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
