<?php

namespace App\Http\Controllers\Catalog;

use App\Entities\Category;
use App\Entities\Limit;
use App\Entities\Offer;
use App\Entities\Product;
use App\Http\Controllers\Controller;
use App\UseCases\Catalog\ProductService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        parent::__construct();
    }

    public function index(Request $request, Category $category = null): View
    {
        $city = $request->cookie('city', config('data.city')[0]);
        if ($category) $title = ' | ' . $category->name;
        else $title = ' | Категории';

//        if ($request['attrs']) {
//            $paginator->with(['values' => function (Builder $query) use ($request) {
//                $query->whereIn('value', $request['attrs']);
//            }]);
//        }
//        $filters = $this->productService->getFilters($productIds);

        $paginator = $this->productService->getProductsByCity($city, $category);
        $cartService = $this->cartService;

        return view('catalog.index', compact('title', 'paginator', 'category', 'cartService'));
    }

    public function sale(Request $request): View
    {
        $title = ' | Распродажа';
        $city = $request->cookie('city', config('data.city')[0]);
        $paginator = $this->productService->getSalesByCity($city);
        $cartService = $this->cartService;

        return view('catalog.sale', compact('title', 'paginator', 'cartService'));
    }

    public function product(Request $request, Product $product): View
    {
        $title = ' | ' . $product->name;
        $city = $request->cookie('city', config('data.city')[0]);

        $offers = $product->offers()->whereCity($city)->get();
        $minPrice = $offers->first()->price;
        foreach ($offers as $offer) {
            if ($minPrice > $offer->price) $minPrice = $offer->price;
        }

        $item = null;
        try {
            $item = $this->cartService->getItem($product->id);
        }
        catch (\DomainException $e) {}

        $cartService = $this->cartService;

        return view('catalog.product', compact('title', 'product', 'offers', 'minPrice', 'item', 'cartService'));
    }

    public function search(Request $request): View
    {
        $title = ' | Поиск';
        $city = $request->cookie('city', config('data.city')[0]);
        if (!$query = $request->query('q'))
            throw new \DomainException('Введите запрос для поиска');

        $paginator = $this->productService->search($query, $city);
        $paginator->appends(['q' => $query]);
        $categories = Category::query()->get()->toTree();
        $cartService = $this->cartService;

        return view('catalog.index', compact('title', 'paginator', 'categories', 'cartService'));
    }

    public function getPrice(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $ip = $request->header('cf-connecting-ip', $request->ip());

            /** @var Limit $limit */
            if ($limit = Limit::query()->where('ip', $ip)->first()) {
                if ($limit->isExpired())
                    throw new \DomainException('Исчерпан лимит на просмотр цен');
            }
            else
                $limit = Limit::create($ip);

            $limit->request();
            $user = Auth::user();
            if ($user and !$limit->user) {
                $limit->reset();
                $limit->user()->associate($user);
            }
            elseif (!$user and $limit->user) {
                $limit->reset();
                $limit->user_id = null;
            }

            if ($limit->expires < Carbon::now())
                $limit->reset();

            $limit->save();

            $city = $request->cookie('city', config('data.city')[0]);
            $offer = Offer::query()->whereCity($city)->where('product_id', $request->query('id'))->orderBy('price')->first();

            return response()->json($offer->price ?? 0);
        }

        throw new NotFoundHttpException('Страница не найдена.');
    }
}