<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Requests\Setting\Banner\UpdateSortRequest;
use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdateSortController
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
            return new JsonResponse([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
