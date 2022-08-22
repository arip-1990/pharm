<?php

namespace App\Http\Controllers;

use App\Models\City;
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
            $this->city = Cookie::get('city', City::query()->find(1)?->name);
        }
        catch (\Exception $exception) {
            $this->city = City::query()->find(1)?->name;
        }
    }

    public function test(Request $request): void
    {
//        DB::listen(function ($query) {
//            dump($query->sql);
//            dump($query->bindings);
//        });
//        $order = Order::query()->orderByDesc('id')->first();
//        dd($order->statuses);
    }
}
