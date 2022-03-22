<?php

namespace App\UseCases\Catalog;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Value;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class ProductService
{
    public function getProductsByCity(string $city, Category $category = null): Paginator
    {
        $categoryIds = new Collection();
        if ($category) {
            $categories = $category->descendants;
            $categoryIds->push($category);
        }
        else {
            $categories = Category::all();
        }

        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        return Product::query()->whereIn('id', $productIds)
            ->whereIn('category_id', $categoryIds->merge($categories)->pluck('id'))->paginate(12);
    }

    public function getSalesByCity(string $city): Paginator
    {
        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        return Product::query()->whereIn('id', [])->paginate(12);
    }

    public function getNamesBySearch(string $text, int $limit = 10): array
    {
        return Product::query()->where('name', 'like', $text . '%')
            ->orWhere('name', 'like', '%' . $text . '%')->limit($limit)->get();
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
