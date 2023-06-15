<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Requests\Setting\Banner\AddRequest;
use App\Setting\Entity\Banner;
use App\Setting\Entity\BannerType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class StoreController
{
    public function __invoke(AddRequest $request): JsonResponse
    {
        try {
            $path = $request->get('path', '/');
            $files = $request->file('files');

            if (!Storage::exists(Banner::getPath($path)) && !Storage::makeDirectory(Banner::getPath($path))) {
                throw new \DomainException(
                    'Не удалось создать папку: ' . Banner::getPath($path),
                    Response::HTTP_INSUFFICIENT_STORAGE
                );
            }

            do {
                $fileName = Str::random() . '.' . strtolower($files['main']->getClientOriginalExtension());
            }
            while (Storage::exists(Banner::getPath($path, $fileName)));

            if (!$files['main']->storeAs(Banner::getPath($path), $fileName)) {
                throw new \DomainException(
                    'Не удалось сохранить фото: ' . Banner::getPath($path, $fileName),
                    Response::HTTP_INSUFFICIENT_STORAGE
                );
            }

            if (isset($files['mobile']))
                $files['mobile']->storeAs(Banner::getPath($path), "mobile_{$fileName}");

            $banner = new Banner([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'picture' => $fileName,
                'path' => $path,
                'link' => $request->get('link'),
                'type' => $request->get('type', BannerType::MAIN),
                'sort' => Banner::where('path', $path)->count()
            ]);

            $banner->creator()->associate($request->user());
            $banner->save();
        }
        catch (\Exception $e) {
            $code = $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

            return new JsonResponse([
                'code' => $code,
                'error' => 'server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
