<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteController
{
    public function __invoke(Request $request, Banner $banner): JsonResponse
    {
        try {
            $banner->destroyer()->associate($request->user())->save();
            $banner->delete();
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
