<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "externalUserId" => "nullable|string",
            "country" => "nullable|string|max:255",
            "hasPreorderItems" => 'nullable|boolean',
            "city" => "nullable|string|max:255",
            "promocode" => "nullable|string|max:255",
            "deliveryId" => "nullable|string",
            "pickupLocationId" => "nullable|integer",
            "bonusesSpent" => "nullable|integer",
            "items" => "nullable|array",
            "items.*.name" => "nullable|string|max:255",
            "items.*.id" => "nullable|string|max:255",
            "items.*.privateId" => "nullable|string",
            "items.*.configurationId" => "nullable|string",
            "items.*.quantity" => "nullable|integer",
        ];
    }
}
