<?php

namespace App\Http\Controllers\Panel\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        return new JsonResponse(Auth::user());
    }
}
