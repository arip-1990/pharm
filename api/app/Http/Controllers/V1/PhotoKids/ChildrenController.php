<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V1\Auth\RegisterController;
use App\Models\numberOfChildren;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Error;


class ChildrenController extends Controller
{
    public function index(User $id)
    {
        $user = Auth::user();

        try {
//            $user = User::query()->where('id', 'f2897002-33e0-4d1e-81b8-041e4b0cd98c')->first();
            return $user->numberOfChildrens->children;
        }catch (\Exception $e){
            return response()->json(0, 200);
        }

    }

    public function show(Request $request)
    {
        try {
            $id = Auth::id();
            $children = numberOfChildren::query()->create([
                "children" => $request->get('count'),
                "user_id" => $id
            ]);
            $children->save();
        }catch (\Exception $e){
            return response()->json(['message' => "Уже указали" ], 200);
        }
    }
}
