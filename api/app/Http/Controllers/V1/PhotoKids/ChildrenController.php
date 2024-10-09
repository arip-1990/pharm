<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V1\Auth\RegisterController;
use App\Models\numberOfChildren;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ChildrenController extends Controller
{
    public function index()
    {
//        $user = Auth::user();
        $user = User::query()->where('id', 'ee374378-12eb-ed11-80cc-001dd8b75065')->first();
        return $user->numberOfChildrens->children;
    }

    public function show(Request $request)
    {
        $id = Auth::id();
        $children = numberOfChildren::query()->create([
            "children" => $request->get('count'),
            "user_id" => "ad97bf84-eb1d-4399-9c9b-d72eb5b72c27"
        ]);
        $children->save();

    }
}
