<?php

namespace App\Http\Controllers;

use App\Entities\Product;
use App\Entities\Offer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request): View
    {
        $title = $this->title;
        $city = $request->cookie('city', $this->defaultCity);

        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id');

        $products = Product::query()->findMany(config('data.productIds'));
        $alphabet = Product::query()->selectRaw('SUBSTRING(name, 1, 1) as abc')->distinct('abc')
            ->whereIn('id', $productIds)->get()->pluck('abc');
        $abc = '';

        $cartItems = $this->cartService->getItems();

        return view('index', compact('title', 'city', 'abc', 'products', 'alphabet', 'cartItems'));
    }

    public function setCity(string $city): RedirectResponse
    {
        return back()->withCookie(cookie('city', $city));
    }

    public function alphabet(Request $request, string $abc): View
    {
        $title = $this->title . '| Список лекарств по алфавиту';
        $city = $request->cookie('city', $this->defaultCity);
        $productIds = Offer::query()->select('product_id')->whereCity($city)
            ->groupBy('product_id')->get()->pluck('product_id')->toArray();
        $alphabet = Product::query()->selectRaw('SUBSTRING(name, 1, 1) as abc')->distinct('abc')
            ->whereIn('id', $productIds)->get()->pluck('abc');

        if($abc === '0-9') $query = Product::query()->where('name', 'REGEXP', '^[0-9].*');
        else $query = Product::query()->where('name', 'like', $abc . '%');

        $paginator = $query->whereIn('id', $productIds)->paginate(30);
        $cartItems = $this->cartService->getItems();

        return view('alphabet', compact('title', 'abc', 'paginator', 'alphabet', 'cartItems'));
    }
}
