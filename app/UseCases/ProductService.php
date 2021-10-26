<?php

namespace App\UseCases;

use App\Entities\Attribute;
use App\Entities\Product;
use App\Entities\Value;
use App\Entities\Offer;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function search(string $text, string $city): Paginator
    {
        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        return Product::query()->whereIn('id', $productIds)->where(function($query) use ($text) {
            $query->where('name', 'like', $text . '%')->orWhere('name', 'like', '%' . $text . '%');
        })->paginate(15);
    }

    public function getNamesBySearch(string $text): array
    {
        return Product::query()->where('name', 'like', $text . '%')
            ->orWhere('name', 'like', '%' . $text . '%')->get();
    }

    public function getFilters(Collection $productIds): array
    {
        $query = Value::query()->whereIn('product_id', $productIds)
            ->with(['attribute' => function ($query) {
                $query->where('type', '!=', Attribute::TYPE_TEXT);
            }]);
        $filters = [];
        foreach ($query->get() as $value) {
            try {
                if (!isset($filters[$value->attribute->name]) or !in_array($value->value, $filters[$value->attribute->name]))
                    $filters[$value->attribute->name][] = $value->value;
            }
            catch (\Exception $e) {
                dd($value);
            }
        }

        return $filters;
    }
}
