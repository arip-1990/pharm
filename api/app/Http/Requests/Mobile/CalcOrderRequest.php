<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class CalcOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'installId' => 'required|uuid',

            'externalUserId' => 'nullable|uuid',
            'loyaltyCard' => 'nullable|string',
            'promocode' => 'nullable|string',

            'city' => 'nullable|string',
            'paymentId' => 'nullable|string',
            'deliveryId' => 'nullable|string',

            'deliveryPickupId' => 'nullable|string',
            'preferredPickupId' => 'nullable|string',

            "addressData" => "required|array",
            "addressData.apt" => "nullable",
            "addressData.area" => "nullable",
            "addressData.building" => "nullable",
            "addressData.city" => "nullable|string",
            "addressData.house" => "nullable|string",
            "addressData.lat" => "nullable|string",
            "addressData.lon" => "nullable|string",
            "addressData.region" => "nullable|string",
            "addressData.settlement" => "nullable|string",
            "addressData.settlementWithType" => "nullable|string",
            "addressData.street" => "nullable|string",
            "addressData.value" =>"nullable|string",
            "addressData.zip" => "nullable|string",

            'giftCards' => 'nullable|array',
            'giftCards.*.pin' => 'required|string',
            'giftCards.*.number' => 'required|string',

            'user' => 'nullable|array',
            'user.name' => 'required|string',
            'user.email' => 'string|nullable',
            'user.phone' => 'required|string',

            'promoGroup' => 'nullable|array',
            'promoGroup.*.id' => 'required|string',
            'promoGroup.*.gifts' => 'nullable|array',
            'promoGroup.*.gifts.*.id' => 'required|string',
            'promoGroup.*.gifts.*.quantity' => 'required|numeric',

            'items' => 'required|array',
            'items.*.id' => 'required|uuid',
            'items.*.privateId' => 'required|uuid',
            'items.*.configurationId' => 'required|uuid',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.subtotal' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.deliveryGroup' => 'nullable|string',
            'items.*.appliedDiscounts' => 'nullable|array'
        ];
    }
}
