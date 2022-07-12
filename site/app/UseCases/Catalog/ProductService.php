<?php

namespace App\UseCases\Catalog;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\Value;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

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

        return Product::active($city)->whereIn('category_id', $categoryIds->merge($categories)->pluck('id'))->paginate(30);
    }

    public function getSalesByCity(string $city): Paginator
    {
        return Product::active($city)->where('sale', true)->paginate(30);
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
