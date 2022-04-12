<?php

namespace App\Http\Controllers;

use App\UseCases\CartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected string $city;
    protected CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
        try {
            $this->city = Cookie::get('city', config('data.city')[0]);
        }
        catch (\Exception $exception) {
            $this->city = config('data.city')[0];
        }
    }

    public function test(Request $request): void
    {
//        DB::listen(function ($query) {
//            dump($query->sql);
//            dump($query->bindings);
//        });
//        $products = Product::query()->findMany(config('data.productIds'));
//        dd($products->first()->getCountByCity($request->cookie('city', $this->defaultCity)));
    }
}
