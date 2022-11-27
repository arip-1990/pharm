<?php

namespace App\Http\Controllers\V1\City;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;

class StoreController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        return (new JsonResponse())->withCookie(Cookie::make('city', $request->get('city')));
    }
}
