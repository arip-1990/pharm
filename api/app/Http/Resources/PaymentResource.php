<?php

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var Payment $this */
       return [
            "id" => $this->slug_id,
            "title" => $this->title,
            "description" => $this->description,
            "type" => $this->type,
            "deliveryDiscount" => 0
        ];
    }
}
