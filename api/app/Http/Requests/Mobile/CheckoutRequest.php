<?php

namespace App\Http\Requests\Mobile;

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
            'installId' => 'nullable|uuid',

            'orders' => 'required|array',
            'orders.*.uuid' => 'nullable|uuid',
            'orders.*.externalUserId' => 'nullable|string',
            'orders.*.groupId' => 'nullable|uuid',
            'orders.*.status' => ['nullable', new Enum(Mobile::class)],
            'orders.*.name' => 'nullable|string',
            'orders.*.phone' => 'nullable|regex:/^7\d{10}$/',
            'orders.*.email' => 'nullable|email',
            'orders.*.price' => 'nullable|numeric',
            'orders.*.deliveryPrice' => 'nullable|numeric',
            'orders.*.authorizedBonuses' => 'nullable|numeric',
            'orders.*.promocode' => 'nullable|string',
            'orders.*.appliedDiscount' => 'nullable|numeric',
            'orders.*.loyaltyCard' => 'nullable|string',
            'orders.*.hasPreorderItems' => 'nullable|boolean',
            'orders.*.externalIds' => 'nullable|array',
            'orders.*.createdOn' => 'nullable|date',
            'orders.*.updatedOn' => 'nullable|date',

            'orders.*.payment' => 'required|string',
            'orders.*.paymentName' => 'required|string',
            'orders.*.paymentProcessed' => 'required|boolean',
            'orders.*.paymentId' => 'required|uuid',
            'orders.*.paymentGateway' => 'nullable|string',

            'orders.*.delivery' => 'required|string',
            'orders.*.deliveryName' => 'required|string',
            'orders.*.deliveryComment' => 'nullable|string',
            'orders.*.pickupLocationId' => 'nullable|uuid',

            'orders.*.city' => 'required|string',
            'orders.*.address' => 'required|string',
            'orders.*.addressData' => 'required|array',
            'orders.*.addressData.city' => 'required|string',
            'orders.*.addressData.region' => 'required|string',
            'orders.*.addressData.street' => 'required|string',
            'orders.*.addressData.house' => 'required|string',
            'orders.*.addressData.building' => 'nullable|string',
            'orders.*.addressData.apt' => 'nullable|string',
            'orders.*.addressData.zip' => 'nullable|string',

            'orders.*.items' => 'required|array',
            'orders.*.items.*.id' => 'required|uuid',
            'orders.*.items.*.privateId' => 'required|string',
            'orders.*.items.*.configurationId' => 'required|string',
            'orders.*.items.*.name' => 'required|string',
            'orders.*.items.*.price' => 'required|numeric',
            'orders.*.items.*.quantity' => 'required|numeric',
            'orders.*.items.*.discount' => 'required|numeric',
            'orders.*.items.*.subtotal' => 'required|numeric',
        ];
    }
}
