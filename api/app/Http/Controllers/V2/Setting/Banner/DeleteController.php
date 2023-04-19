<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DeleteController extends Controller
{
    public function __invoke(DeleteController $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($banner = Banner::find($item)) {
                    $banner->destroyer()->associate($request->user())->save();
                    $banner->delete();
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), options: JSON_UNESCAPED_UNICODE);
        }

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
