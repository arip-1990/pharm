<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Requests\Panel\Product\UpdatePhotosRequest;
use App\Product\Entity\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdatePhotoController extends Controller
{
    public function handle(UpdatePhotosRequest $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($photo = Photo::find($item['id'])) {
                    $photo->sort = $item['sort'];
                    $photo->creator()->associate($request->user());

                    $photo->save();
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), options: JSON_UNESCAPED_UNICODE);
        }

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
