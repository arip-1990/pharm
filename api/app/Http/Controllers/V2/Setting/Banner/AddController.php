<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Requests\Setting\Banner\AddRequest;
use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AddController
{
    public function __invoke(AddRequest $request): JsonResponse
    {
        try {
            $files = $request->file('files');
            do {
                $fileName = Str::random() . '.' . strtolower($files['main']->getClientOriginalExtension());
            }
            while (Storage::exists(Banner::getPath($fileName)));

            if (!$files['main']->storeAs(Banner::getPath(), $fileName))
                throw new \DomainException('Не удалось сохранить фото', Response::HTTP_INSUFFICIENT_STORAGE);
            
            if (isset($files['mobile'])) $files['mobile']->storeAs(Banner::getPath(), "mobile_{$fileName}");

            $banner = new Banner([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'picture' => $fileName,
                'type' => Banner::TYPE_MAIN,
                'sort' => Banner::query()->count()
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
            ], $code);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }
}
