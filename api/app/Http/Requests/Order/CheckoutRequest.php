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
            'device.platform' => 'nullable',
            'orders.*.uuid' => 'nullable|uuid',
            'orders.*.groupId' => 'nullable|uuid',
            'orders.*.status' => ['nullable', new Enum(Mobile::class)],
            'orders.*.name' => 'nullable',
            'orders.*.phone' => 'nullable',
            'orders.*.email' => 'nullable|email',
            'orders.*.price' => 'nullable|numeric',
            'orders.*.deliveryPrice' => 'nullable|numeric',
            'orders.*.authorizedBonuses' => 'nullable|numeric',
            'orders.*.promocode' => 'nullable',
            'orders.*.appliedDiscount' => 'nullable|numeric',
            'orders.*.loyaltyCard' => 'nullable',
            'orders.*.hasPreorderItems' => 'nullable|boolean',
            'orders.*.createdOn' => 'nullable|date',
            'orders.*.updatedOn' => 'nullable|date',

            'orders.*.delivery' => 'required',
            'orders.*.deliveryName' => 'required',
            'orders.*.deliveryComment' => 'required',

            'orders.*.payment' => 'required',
            'orders.*.paymentName' => 'required',
            'orders.*.paymentProcessed' => 'required|boolean',
            'orders.*.paymentId' => 'required|uuid',
            'orders.*.paymentGateway' => 'required',

            'orders.*.city' => 'required',
            'orders.*.address' => 'required',
            'orders.*.addressData' => 'nullable|array',
            'orders.*.addressData.city' => 'required',
            'orders.*.addressData.region' => 'required',
            'orders.*.addressData.street' => 'required',
            'orders.*.addressData.house' => 'required',
            'orders.*.addressData.apt' => 'nullable',

            'orders.*.items.*.id' => 'required|uuid',
            'orders.*.items.*.privateId' => 'required',
            'orders.*.items.*.configurationId' => 'required',
            'orders.*.items.*.name' => 'required',
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
