<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "externalUserId" => "nullable|string",
            "country" => "nullable|string",
            "city" => "nullable|string",
            "hasPreorderItems" => "nullable|boolean",
            "skipPickupLocations" => "nullable|boolean",
            "promocode" => "nullable|string",
            "bonusesSpent" => "nullable|numeric",
            "position" => "nullable|string",

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

            "items" => "required|array",
            "items.*.name" => "nullable|string",
            "items.*.id" => "nullable|uuid",
            "items.*.privateId" => "required|uuid",
            "items.*.configurationId" => "nullable|uuid",
            "items.*.quantity" => "nullable|numeric",
//            'items.*.deliveryGroup' => 'nullable|string',
        ];
    }
}
