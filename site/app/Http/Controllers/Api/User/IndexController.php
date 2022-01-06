<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function handle(): JsonResponse
    {
        $user = Auth::user();

        return response()->json($user);
    }
}
