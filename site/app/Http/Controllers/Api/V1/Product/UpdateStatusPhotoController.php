<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Requests\Api\Product\DeletePhotosRequest;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateStatusPhotoController extends Controller
{
    public function handle(DeletePhotosRequest $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($photo = Photo::query()->find($item)) {
                    $photo->update(['status' => Photo::STATUS_CHECKED]);
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
