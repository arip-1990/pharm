<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Entities\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->session()->get('favorites', []);
        $paginator = Product::query()->active()->whereIn('id', $favorites)->paginate(15);
        $cartService = $this->cartService;

        return view('catalog.favorite', compact('paginator', 'cartService'));
    }

    public function add(Request $request, string $id): JsonResponse
    {
        $ids = $request->session()->pull('favorites', []);
        $ids[] = $id;
        $ids = array_unique($ids);
        $request->session()->put('favorites', $ids);

        return new JsonResponse(['total' => count($ids)], Response::HTTP_CREATED);
    }

    public function remove(Request $request, string $id): JsonResponse
    {
        $ids = $request->session()->pull('favorites', []);
        foreach ($ids as $i => $item) {
            if ($item === $id) unset($ids[$i]);
        }
        $request->session()->put('favorites', $ids);

        return new JsonResponse(['total' => count($ids)], Response::HTTP_ACCEPTED);
    }
}
