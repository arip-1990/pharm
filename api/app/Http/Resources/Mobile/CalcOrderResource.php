<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class CalcOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'totalPrice' => $this['totalPrice'],
            'appliedPromocode' => null,
//            'discount' => $this->discount,
            'items' => $this['items'],
            'bonuses' => [
                'canSpend' => 0,
                'willEarn' => 0
            ],
        ];
    }
}
