<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserPhotoController extends Controller
{
    public function index(User $id)
    {
        return $id->photo_kids;
    }
}
