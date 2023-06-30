<?php

namespace App\Product\UseCase;

use App\Product\Entity\{Attribute, Category, Offer, Product, Value};
use Cviebrock\EloquentSluggable\Services\SlugService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

        $productIds = Offer::select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        return Product::whereIn('id', $productIds)
            ->whereIn('category_id', $categoryIds->merge($categories)->pluck('id'))->paginate(12);
    }

    public function getSalesByCity(string $city): Paginator
    {
        $productIds = Offer::select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        return Product::whereIn('id', [])->paginate(12);
    }

    public function getNamesBySearch(string $text, int $limit = 10): array
    {
        return Product::where('name', 'like', $text . '%')
            ->orWhere('name', 'like', '%' . $text . '%')->limit($limit)->get();
    }

    public function getFilters(Collection $productIds): array
    {
        $query = Value::whereIn('product_id', $productIds)
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

    public function updateData(): void
    {
        $config = config('services.1c');
        try {
            $client = new Client([
                'base_uri' => $config['base_url'],
                'auth' => [$config['login'], $config['password']],
                'verify' => false
            ]);

            $response = $client->get($config['urls'][0]);
            $xml = simplexml_load_string($response->getBody()->getContents());
            if ($xml === false)
                throw new \DomainException('Ошибка парсинга xml');

            $productFields = [];
            $valueFields = [];
            $i = 0;
            foreach ($xml->goods->good as $item) {
                $productFields[] = [
                    'id' => (string) $item->uuid,
                    'category_id' => (int) $item->category ?: null,
                    'name' => (string) $item->name,
                    'code' => (int) $item->code,
                    'recipe' => (string) $item->recipe === 'true' ? true : (Product::find((string) $item->uuid)?->recipe ?? false),
                    'marked' => (string) $item->is_marked === 'true',
                ];

                if ($vendor = (string) $item->vendor) {
                    $valueFields[] = [
                        'attribute_id' => 1,
                        'product_id' => (string) $item->uuid,
                        'value' => $vendor
                    ];
                }

                $i++;

                if ($i >= 1000) {
                    Product::upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe']);
                    Value::upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
                    $productFields = [];
                    $valueFields = [];
                    $i = 0;
                }
            }

            if ($i) {
                Product::upsert($productFields, 'code', ['id', 'category_id', 'name', 'marked', 'recipe']);
                Value::upsert($valueFields, ['attribute_id', 'product_id'], ['product_id', 'value']);
            }

            foreach (Product::whereNull('slug')->get() as $product) {
                $product->update(['slug' => SlugService::createSlug(Product::class, 'slug', $product->name)]);
            }
        } catch (\Exception | GuzzleException $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}
