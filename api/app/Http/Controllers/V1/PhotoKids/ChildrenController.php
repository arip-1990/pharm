<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Http\Controllers\V1\Auth\RegisterController;
use App\Models\numberOfChildren;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ChildrenController extends Controller
{
    public function index(User $id)
    {
        return $id->numberOfChildrens->children;
    }

    public function show(Request $request)
    {
        $id = Auth::id();
        $children = numberOfChildren::query()->create([
            "children" => $request->get('count'),
            "user_id" => "70277a84-013c-ed11-80cb-001dd8b75065"
        ]);
        $children->save();

    }
}
