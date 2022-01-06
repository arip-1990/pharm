<?php

namespace App\Repositories;

use App\Entities\Photo;
use App\Entities\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function getAll(Request $request): Collection
    {
        $current = (int)$request->get('page', 1);
        $pageSize = (int)$request->get('pageSize', 10);
        $query = Product::query()->select('products.*');
        
        if ($status = $request->get('status'))
            $query->where('status', $status === 'on' ? Product::STATUS_ACTIVE : Product::STATUS_DRAFT);
        
        if ($photo = $request->get('photo'))
            $photo === 'on' ? $query->has('photos') : $query->doesnthave('photos');
        
        if ($category = $request->get('category'))
            $category === 'on' ? $query->whereNotNull('category_id') : $query->whereNull('category_id');

        if ($request->get('searchColumn'))
            $query->where($request->get('searchColumn'), 'like', $request->get('searchText') . '%');
        
        $field = $request->get('orderField');
        if ($field) {
            if ($field === 'category')
                $query->join('categories', 'categories.id', '=', 'products.category_id')->orderBy('categories.name', $request->get('orderDirection'));
            else
                $query->orderBy($field, $request->get('orderDirection'));
        }
        
        $total = $query->count();
        $products = $query->skip(($current - 1) * $pageSize)->take($pageSize)->get()->map(function (Product $product) {
            $attributes = [];
            foreach($product->values as $value) {
                $attributes[] = [
                    'attrubuteName' => $value->attribute->name,
                    'attrubuteType' => $value->attribute->type,
                    'value' => $value->value,
                ];
            }

            $photos = [];
            foreach($product->photos as $photo) {
                $photos[] = [
                    'id' => $photo->id,
                    'url' => url($photo->getOriginalFile())
                ];
            }
            if (!count($photos)) $photos[] = ['id' => null, 'url' => url(Photo::DEFAULT_FILE)];

            return [
                'id' => $product->id,
                'slug' => $product->slug,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name
                ] : null,
                'name' => $product->name,
                'code' => $product->code,
                'barcode' => $product->barcode,
                'photos' => $photos,
                'description' => $product->description,
                'status' => $product->status ? 'Активен' : 'Не активен',
                'marked' => $product->marked,
                'attributes' => $attributes,
                'createdAt' => $product->created_at,
                'updatedAt' => $product->updated_at,
            ];
        });

        return new Collection([
            'current' => $current,
            'pageSize' => $pageSize,
            'total' => $total,
            'data' => $products
        ]);
    }
}
