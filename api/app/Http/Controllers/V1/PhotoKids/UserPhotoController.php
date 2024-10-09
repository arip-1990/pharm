<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPhotoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $count = count($user->photo_kids);
        return response()->json(['count' => $count]);
    }
}
