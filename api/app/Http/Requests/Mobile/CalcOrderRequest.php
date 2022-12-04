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

            'city' => 'required|string',
            'cityKladr' => 'required|string',
            'fiasCode' => 'required|string',
            'deliveryId' => 'nullable|string',
            'paymentId' => 'nullable|string',

            'deliveryPickupId' => 'nullable|string',
            'preferredPickupId' => 'nullable|string',

            'giftCards' => 'nullable|array',
            'giftCards.*.pin' => 'required|string',
            'giftCards.*.number' => 'required|string',

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
            'items.*.appliedDiscounts' => 'nullable|string'
        ];
    }
}
