<?php

namespace App\Http\Controllers\Api\V1\Offer;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Value;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;

class ImShopController extends Controller
{
    public function handle(): string
    {
        $date = Carbon::now()->format('Y-m-d h:i');
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= "<yml_catalog date='{$date}'><shop>";
        $data .= $this->generateCategories();
        $data .= $this->generateOffers();
        $data .= '</shop></yml_catalog>';
        return $data;
    }

    private function generateCategories(): string
    {
        $data = '<categories>';
        /** @var Category $category */
        foreach (Category::query()->orderBy('_lft')->get() as $category) {
            if ($category->parent_id) {
                $data .= "<category id='{$category->id}' parentId='{$category->parent_id}'>{$category->name}</category>";
            }
            else {
                $data .= "<category id='{$category->id}'>{$category->name}</category>";
            }
        }
        $data .= '</categories>';
        return $data;
    }

    private function generateOffers(): string
    {
        $data = '<offers>';
        Product::query()->has('offers')->chunk(1000, function ($products) use (&$data) {
            /** @var Product $product */
            foreach ($products as $product) {
                /** @var Offer $offer */
                $offer = $product->offers()->first();
                $url = route('catalog.product', ['product' => $product]);
                $tmp = "<offer id='{$offer->id}' available='true'>";
                if ($product->barcode) {
                    $tmp .= "<barcode>{$product->barcode}</barcode>";
                }
                $tmp .= "<name>{$product->name}</name>";
                $tmp .= "<url>{$url}</url>";
                if ($product->getValue(1)) {
                    $tmp .= "<vendor>{$product->getValue(1)}</vendor>";
                }
                if ($product->category_id) {
                    $tmp .= "<typePrefix>{$product->category->name}</typePrefix>";
                    $tmp .= "<categoryId>{$product->category_id}</categoryId>";
                }
                if ($product->checkedPhotos->count()) {
                    foreach ($product->checkedPhotos as $photo) {
                        $tmp .= "<picture>{$photo->getUrl()}</picture>";
                    }
                }
                $tmp .= "<currencyId>RUR</currencyId><price>{$offer->price}</price>";
                if ($product->description) {
                    $tmp .= "<description>{$product->description}</description>";
                }

                $tmp .= $this->generateParams($product);

                $tmp .= '</offer>';
                $data .= $tmp;
            }
        });
        $data .= '</offers>';
        return $data;
    }

    private function generateParams(Product $product): string
    {
        $data = '';
        $values = $product->values()->whereRelation('attribute', function (Builder $builder) {
            $builder->where('type', 'string');
        })->get();
        /** @var Value $value */
        foreach ($values as $value) {
            $data .= "<param name='{$value->attribute->name}'>{$value->value}</param>";
        }
        return $data;
    }
}
