<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Requests\Setting\Banner\UpdateSortRequest;
use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UpdateSortController extends Controller
{
    public function __invoke(UpdateSortRequest $request): JsonResponse
    {
        try {
            foreach ($request->get('items') as $item) {
                if ($banner = Banner::find($item['id'])) {
                    $banner->sort = $item['sort'];
                    $banner->creator()->associate($request->user());

                    $banner->save();
                }
            }
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), options: JSON_UNESCAPED_UNICODE);
        }

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
