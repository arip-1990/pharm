<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ImShopController
{
    public function handle(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
            <yml_catalog date="' . Carbon::now()->format('Y-m-d H:i') . '">
                <shop>
                    <categories>' . $this->generateCategories() . '</categories>
                    <offers>' . $this->generateOffers() . '</offers>
                </shop>
            </yml_catalog>';
    }

    private function generateCategories(): string
    {
        $yml = '';
        foreach (Category::all() as $category) {
            $yml .= '<category id="' . $category->id . '"' . ($category->parent_id ? (' parentId="' . $category->parent_id . '"') : '') . '>' . $category->name . '</category>';
        }

        return $yml;
    }

    private function generateOffers(): string
    {
        $yml = '';
        Product::query()->whereNotNull('description')->has('offers')
            ->whereHas('values', function (Builder $builder) {
            $builder->whereIn('attribute_id', [45, 32, 30, 31, 34, 35, 47, 48, 33]);
        })->each(function (Product $product) use (&$yml) {
            $yml .= '<offer id="' . $product->id . '" available="true">';
                if ($product->barcode) $yml .= '<barcode>' . $product->barcode . '</barcode>';
                $yml .= '<name>' . $product->name . '</name>';
                $yml .= '<url>' . url('/product', ['product' => $product]) . '</url>';
                if ($vendor = $product->getValue(1)) $yml .= '<vendor>' . $vendor . '</vendor>';
                if ($product->category_id) {
                    $yml .= '<typePrefix>' . $product->category->name . '</typePrefix>';
                    $yml .= '<categoryId>' . $product->category_id . '</categoryId>';
                }
                if ($product->photos_count) {
                    foreach ($product->photos as $photo) $yml .= '<picture>' . $photo->getUrl() . '</picture>';
                }
                $yml .= '<currencyId>RUR</currencyId>';
                $yml .= '<price>' . $product->getPrice() . '</price>';
            $yml .= '</offer>';
        });
        return $yml;
    }
}
