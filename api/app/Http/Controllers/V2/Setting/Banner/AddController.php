<?php

namespace App\Http\Controllers\V2\Setting\Banner;

use App\Http\Requests\Setting\Banner\AddRequest;
use App\Setting\Entity\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AddController extends Controller
{
    public function __invoke(AddRequest $request): JsonResponse
    {
        try {
            if (!$request->hasFile('file') or !$request->file('file')->isValid())
                throw new \DomainException('Файл не определен!');

            $image = $request->file('file');
            do {
                $fileName = Str::random() . '.' . strtolower($image->getClientOriginalExtension());
            }
            while (Storage::exists(Banner::getPath($fileName)));

            if (!$image->storeAs(Banner::getPath(), $fileName))
                throw new \DomainException('Не удалось сохранить фото');

            $banner = new Banner([
                'title' => explode('.', $image->getClientOriginalName())[0],
                'picture' => $fileName,
                'type' => Banner::TYPE_MAIN,
                'sort' => Banner::query()->count()
            ]);

            $banner->creator()->associate($request->user());
            $banner->save();
        }
        catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500, options: JSON_UNESCAPED_UNICODE);
        }

        return new JsonResponse(options: JSON_UNESCAPED_UNICODE);
    }
}
