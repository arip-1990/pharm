<?php

namespace App\Http\Resources;

use App\Store\Entity\City;
use App\Product\Entity\{Product, Value};
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request): array
    {

        /** @var Product $this */
        $discount = $this->discounts->first();
        $discount_price = 0;

        $percent = $discount?->percent;
        $rubles = $discount?->rubles;
        $quantity = $discount?->quantity;

        $min_price = $this->getPrice();
//        if ($rubles != 0) {
//            $discount_price = $min_price + $rubles;
//        }else if ($percent != 0 ) {
//            $discount_price = $min_price * ($percent / 100);
//        }

        if ($rubles && $rubles > 0) {
            $discount_price = $min_price - $rubles;
        } elseif ($percent && $percent > 0) {
            $discount_price = $min_price - ($min_price * ($percent / 100));
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category ? new CategoryResource($this->category) : null,
            'name' => $this->name,
            'code' => $this->code,
            'barcodes' => $this->barcodes,
            'photos' => PhotoResource::collection($this->photos),
            'description' => $this->description,
            'status' => $this->status,
            'marked' => $this->marked,
            'recipe' => $this->recipe,
            'showMain' => $this->statistic?->show ?? false,
            'attributes' => $this->values->map(fn(Value $value) => [
                'id' => $value->attribute->id,
                'name' => $value->attribute->name,
                'type' => $value->attribute->type,
                'variants' => $value->attribute->variants,
                'value' => $value->value,
            ]),
            'discount' => $this->discounts->first()?->percent,
            'rubles' => $this->discounts->first()?->rubles,
            'quantity' => $this->discounts->first()?->quantity,
//            'offers' => OfferResource::collection($this->offers),
            'offers' => OfferResource::collection(
                $this->offers->map(function ($offer) use ($percent, $rubles) {
                    $offer->discount_percent = $percent;
                    $offer->discount_rubles = $rubles;
                    return $offer;
                })
            ),
            'minPrice' => $this->getPrice(),
            'totalOffers' => $this->getCountByCity($request->cookie('city', City::find(1)?->name)),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'discountPrice' => $discount_price,
        ];
    }
}
