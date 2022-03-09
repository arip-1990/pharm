<?php

namespace App\Http\Controllers;

use App\Entities\Category;
use App\Entities\Product;
use App\Entities\Offer;
use App\Entities\ProductStatistic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request): View
    {
        $city = $request->cookie('city', config('data.city')[0]);

        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');
        $popularIds = ProductStatistic::query()->select('id')->whereIn('id', $productIds)
            ->orderByDesc('orders')->orderByDesc('views')->get()->pluck('id');
        $categoryIds = Category::query()->whereIn('id', [536, 556])->get()
            ->map(fn(Category $category) => $category->descendants->pluck('id'))->collapse();

        $products = Product::query()->whereNotIn('category_id', $categoryIds->push(536, 556))->whereIn('id', $popularIds)->take(12)->get();
        $alphabet = Product::query()->selectRaw('SUBSTRING(name, 1, 1) as abc')->distinct('abc')
            ->whereIn('id', $productIds)->get()->pluck('abc');
        $abc = '';

        $cartService = $this->cartService;

        return view('index', compact('abc', 'products', 'alphabet', 'cartService'));
    }

    public function setCity(string $city): RedirectResponse
    {
        return back()->withCookie(cookie('city', $city));
    }

    public function alphabet(Request $request, string $abc): View
    {
        $title = ' | Список лекарств по алфавиту';
        $city = $request->cookie('city', config('data.city')[0]);
        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id')->toArray();
        $alphabet = Product::query()->selectRaw('SUBSTRING(name, 1, 1) as abc')->distinct('abc')
            ->whereIn('id', $productIds)->get()->pluck('abc');

        if($abc === '0-9') $query = Product::query()->where('name', 'REGEXP', '^[0-9].*');
        else $query = Product::query()->where('name', 'like', $abc . '%');

        $paginator = $query->whereIn('id', $productIds)->paginate(30);
        $cartService = $this->cartService;

        return view('alphabet', compact('title', 'abc', 'paginator', 'alphabet', 'cartService'));
    }
}
