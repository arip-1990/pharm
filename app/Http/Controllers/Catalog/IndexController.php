<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Entities\Category;
use App\Entities\Product;
use App\UseCases\CartService;
use App\UseCases\ProductService;
use App\Entities\Offer;
use App\Entities\Limit;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
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
        $city = $request->cookie('city', $this->defaultCity);
        $categoryIds = new Collection();
        if ($category) {
            $title = $this->title . ' | ' . $category->name;
            $categories = $category->descendants;
            $categoryIds->push($category);
        }
        else {
            $title = $this->title . ' | Категории';
            $categories = Category::query()->get();
        }
        $categoryIds = $categoryIds->merge($categories)->pluck('id');
        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        $paginator = Product::query()
            ->whereIn('id', $productIds)
            ->whereIn('category_id', $categoryIds);

//        if ($request['attrs']) {
//            $paginator->with(['values' => function (Builder $query) use ($request) {
//                $query->whereIn('value', $request['attrs']);
//            }]);
//        }
//        $filters = $this->productService->getFilters($productIds);

        $categories = $categories->toTree();
        $paginator = $paginator->paginate(12);
        $cartItems = $this->cartService->getItems();

        return view('catalog.index', compact('title', 'city', 'paginator', 'categories', 'cartItems'));
    }

    public function product(Request $request, Product $product): View
    {
        $title = $this->title . ' | ' . $product->name;
        $city = $request->cookie('city', $this->defaultCity);

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

        $cartItems = $this->cartService->getItems();

        return view('catalog.product', compact('title', 'city', 'product', 'offers', 'minPrice', 'item', 'cartItems'));
    }

    public function search(Request $request): View
    {
        $title = $this->title . ' | Поиск';
        $city = $request->cookie('city', $this->defaultCity);
        if (!$query = $request->query('q'))
            throw new \DomainException('Введите запрос для поиска');

        $paginator = $this->productService->search($query, $city);
        $paginator->appends(['q' => $query]);
        $categories = Category::query()->get()->toTree();
        $cartItems = $this->cartService->getItems();

        return view('catalog.index', compact('title', 'city', 'paginator', 'categories', 'cartItems'));
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

            $city = $request->cookie('city', $this->defaultCity);
            $offer = Offer::query()->whereCity($city)->where('product_id', $request->query('id'))->orderBy('price')->first();

            return response()->json($offer->price ?? 0);
        }

        throw new NotFoundHttpException('Страница не найдена.');
    }
}
