<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Entities\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $title = $this->title;
        $favorites = session('favorites', new Collection());
        $paginator = Product::query()->active()->whereIn('id', $favorites)->paginate(15);

        return view('catalog.favorite', compact('title', 'paginator'));
    }

    public function add(string $id): JsonResponse
    {
        $ids = session('favorites', []);
        $ids[] = $id;
        $ids = array_unique($ids);
        session(['favorites' => $ids]);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    public function remove(string $id): JsonResponse
    {
        $ids = session('favorites', []);
        if(($key = array_search($id, $ids)) !== false)
            unset($ids[$key]);
        session(['favorites' => $ids]);

        return new JsonResponse();
    }
}
