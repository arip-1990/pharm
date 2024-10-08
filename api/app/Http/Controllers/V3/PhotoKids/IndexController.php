<?php

namespace App\Http\Controllers\V3\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\PhotoKids;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request):object
    {
        return PhotoKids::query()->where("published", $request->get('flag'))->get();
    }
}
