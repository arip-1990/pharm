<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Requests\Api\Product\UpdatePhotosRequest;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdatePhotoController extends Controller
{
    public function handle(UpdatePhotosRequest $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($photo = Photo::query()->find($item['id'])) {
                    $photo->update(['sort' => $item['sort']]);
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
