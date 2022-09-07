<?php

namespace App\Http\Controllers\City;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class StoreController
{
    public function handle(Request $request): JsonResponse
    {
        return (new JsonResponse())->withCookie(Cookie::make('city', $request->get('city')));
    }
}
