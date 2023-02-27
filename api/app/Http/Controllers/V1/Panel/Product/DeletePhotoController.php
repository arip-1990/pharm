<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Requests\Panel\Product\DeletePhotosRequest;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DeletePhotoController extends Controller
{
    public function handle(DeletePhotosRequest $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($photo = Photo::find($item)) {
                    $photo->destroyer()->associate($request->user())->save();
                    $photo->delete();
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
