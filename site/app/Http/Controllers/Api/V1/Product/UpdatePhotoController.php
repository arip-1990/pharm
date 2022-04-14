<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Requests\Api\Product\UpdatePhotosRequest;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdatePhotoController extends Controller
{
    public function handle(Product $product, UpdatePhotosRequest $request): JsonResponse
    {
        try {
            $updatePhotos = [];
            foreach ($request->get('items') as $item) {
                if ($photo = Photo::query()->find($item['id'])) {
                    $photo->sort = $item['sort'];
                    $updatePhotos[] = $photo;
                }
            }
            $product->photos()->saveMany($updatePhotos);
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
