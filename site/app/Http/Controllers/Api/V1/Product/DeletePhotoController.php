<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Requests\Api\Product\DeletePhotosRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class DeletePhotoController extends Controller
{
    public function handle(Product $product, DeletePhotosRequest $request): JsonResponse
    {
        try {
            $deletedPhotos = [];
            foreach ($request->get('items') as $item) {
                if (unlink(glob(Storage::path("images/original/{$product->id}") . "/{$item}.*")[0])) {
                    $deletedPhotos[] = $item;
                }
            }
            $product->photos()->whereIn('id', $deletedPhotos)->delete();
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
