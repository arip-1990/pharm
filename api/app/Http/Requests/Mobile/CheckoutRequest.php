<?php

namespace App\Http\Requests\Mobile;

use App\Models\Status\MobilePlatform;
use App\Models\Status\MobileStatus;
use App\Rules\CustomDate;
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
            'device.platform' => ['nullable', new Enum(MobilePlatform::class)],
            'installId' => 'nullable|uuid',

            'orders' => 'required|array',
            'orders.*.uuid' => 'nullable|uuid',
            'orders.*.externalUserId' => 'nullable|uuid',
            'orders.*.groupId' => 'nullable|uuid',
            'orders.*.status' => ['nullable', new Enum(MobileStatus::class)],
            'orders.*.name' => 'nullable|string',
            'orders.*.phone' => 'nullable|regex:/^\+7\d{10}$/',
            'orders.*.email' => 'nullable|email',
            'orders.*.price' => 'nullable|numeric',
            'orders.*.doNotCallMe' => 'nullable|boolean',
            'orders.*.deliveryPrice' => 'nullable|numeric',
            'orders.*.authorizedBonuses' => 'nullable|numeric',
            'orders.*.promocode' => 'nullable|string',
            'orders.*.appliedDiscount' => 'nullable|numeric',
            'orders.*.loyaltyCard' => 'nullable|string',
//            'orders.*.hasPreorderItems' => 'nullable|boolean',
            'orders.*.externalIds' => 'nullable|array',
            'orders.*.createdOn' => ['nullable', new CustomDate()],
            'orders.*.updatedOn' => ['nullable', new CustomDate()],

            'orders.*.payment' => 'required|string',
            'orders.*.delivery' => 'required|string',
            'orders.*.deliveryDate' => 'nullable',
            'orders.*.deliveryComment' => 'nullable|string',
            'orders.*.pickupLocationId' => 'nullable|uuid',

            'orders.*.city' => 'required|string',
            'orders.*.address' => 'nullable|string',
            'orders.*.addressData' => 'required|array',
            'orders.*.addressData.region' => 'required|string',
            'orders.*.addressData.city' => 'required|string',
            'orders.*.addressData.street' => 'nullable|string',
            'orders.*.addressData.house' => 'nullable|string',
            'orders.*.addressData.building' => 'nullable|string',
            'orders.*.addressData.apt' => 'nullable|string',
            'orders.*.addressData.zip' => 'nullable|string',
            'orders.*.addressData.lat' => 'nullable|numeric',
            'orders.*.addressData.lon' => 'nullable|numeric',

            'orders.*.items' => 'required|array',
            'orders.*.items.*.id' => 'required|uuid',
            'orders.*.items.*.privateId' => 'required|uuid',
            'orders.*.items.*.configurationId' => 'required|uuid',
            'orders.*.items.*.name' => 'required|string',
            'orders.*.items.*.price' => 'required|numeric',
            'orders.*.items.*.quantity' => 'required|numeric',
            'orders.*.items.*.discount' => 'required|numeric',
            'orders.*.items.*.subtotal' => 'required|numeric',
        ];
    }
}
