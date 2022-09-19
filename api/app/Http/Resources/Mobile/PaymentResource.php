<?php

namespace App\Http\Resources\Mobile;

use App\Models\Payment;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Payment $this */
       return [
            "id" => (string)$this->id,
            "title" => $this->title,
            "description" => $this->description,
            "type" => $this->type,
            "deliveryDiscount" => 0
        ];
    }
}
