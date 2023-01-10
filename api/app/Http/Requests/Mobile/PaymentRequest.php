<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "externalUserId" => "nullable|string",
            "hasPreorderItems" => 'nullable|boolean',
            'city' => 'required|string',
            "promocode" => "nullable|string",
            "deliveryId" => "nullable|string",
            "pickupLocationId" => "nullable|uuid",
            "bonusesSpent" => "nullable|integer",

            "items" => "nullable|array",
            "items.*.name" => "nullable|string",
            "items.*.id" => "nullable|string",
            "items.*.privateId" => "nullable|string",
            "items.*.configurationId" => "nullable|string",
            "items.*.quantity" => "nullable|integer",
        ];
    }
}
