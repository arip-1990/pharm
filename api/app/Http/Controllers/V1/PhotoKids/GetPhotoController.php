<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\AgeCategoryModel;

class GetPhotoController extends Controller
{
    public function index(AgeCategoryModel $age)
    {
        return $age->photos()->where('published', true)->with('UsersLikes')->with('age_category')->get();

    }
}
