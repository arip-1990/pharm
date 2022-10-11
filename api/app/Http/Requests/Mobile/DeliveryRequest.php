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
            "externalUserId" => "nullable|string|max:255",
            "country" => "nullable|string|max:255",
            "hasPreorderItems" => 'nullable|boolean',
            "promocode" => "nullable|string|max:255",
            "bonusesSpent" => "nullable|numeric",
            "position" => "nullable|string|max:255",

            "addressData.apt" => "nullable",
            "addressData.area" => "nullable",
            "addressData.areaFias" => "nullable",
            "addressData.areaKladr" => "nullable",
            "addressData.building" => "nullable",
            "addressData.city" => "nullable|string|max:255",
            "addressData.cityFias" => "nullable|string|max:255",
            "addressData.cityKladr" => "nullable|string|max:255",
            "addressData.city_kladr" => "nullable|string|max:255",
            "addressData.fias" => "nullable|string|max:255",
            "addressData.fiasCode" => "nullable|string|max:255",
            "addressData.fias_code" => "nullable|string|max:255",
            "addressData.fias_id" => "nullable|string|max:255",
            "addressData.house" => "nullable",
            "addressData.houseFias" => "nullable",
            "addressData.houseKladr" => "nullable",
            "addressData.kladr" => "nullable|string|max:255",
            "addressData.lat" => "nullable|string|max:255",
            "addressData.lon" => "nullable|string|max:255",
            "addressData.region" => "nullable|string|max:255",
            "addressData.regionFias" => "nullable|string|max:255",
            "addressData.regionKladr" => "nullable|string|max:255",
            "addressData.settlement" => "nullable",
            "addressData.settlementFias" => "nullable",
            "addressData.settlementKladr" => "nullable",
            "addressData.settlementWithType" => "nullable",
            "addressData.street" => "nullable",
            "addressData.streetFias" => "nullable",
            "addressData.streetKladr" => "nullable",
            "addressData.value" =>"nullable|string|max:255",
            "addressData.zip" => "nullable|string|max:255",

            "items" => "required|array",
            "items.*.name" => "nullable|string|max:255",
            "items.*.id" => "nullable|uuid|max:255",
            "items.*.privateId" => "required|uuid|max:255",
            "items.*.configurationId" => "nullable|uuid|max:255",
            "items.*.quantity" => "nullable|numeric",
        ];
    }
}
