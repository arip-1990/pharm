<?php

namespace App\Http\Requests\Payment;

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
            "country" => "nullable|string|max:255",
            "hasPreorderItems" => 'nullable|boolean',
            "city" => "nullable|string|max:255",
            "promocode" => "nullable|string|max:255",
            "deliveryId" => "nullable|integer",
            "pickupLocationId" => "nullable|integer",
            "bonusesSpent" => "nullable|integer",
            "items" => "nullable|array",
            "items.*.name" => "nullable|string|max:255",
            "items.*.id" => "nullable|string|max:255",
            "items.*.privateId" => "nullable|integer",
            "items.*.configurationId" => "nullable|integer",
            "items.*.quantity" => "nullable|integer",
        ];
    }
}
