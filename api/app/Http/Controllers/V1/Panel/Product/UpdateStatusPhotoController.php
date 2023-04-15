<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Requests\Panel\Product\DeletePhotosRequest;
use App\Product\Entity\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateStatusPhotoController extends Controller
{
    public function handle(DeletePhotosRequest $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($photo = Photo::find($item)) {
                    $photo->update(['status' => Photo::STATUS_CHECKED]);
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), options: JSON_UNESCAPED_UNICODE);
        }

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
