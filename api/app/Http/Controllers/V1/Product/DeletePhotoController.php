<?php

namespace App\Http\Controllers\V1\Product;

use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class DeletePhotoController extends Controller
{
    public function handle(Photo $photo): JsonResponse
    {
        try {
            unlink(glob(Storage::path("images/original/{$photo->product_id}") . "/DeletePhotoController.php")[0]);
            $photo->delete();
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }

        return new JsonResponse();
    }
}
