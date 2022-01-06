<?php

namespace App\Http\Controllers;

use App\UseCases\CartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
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
