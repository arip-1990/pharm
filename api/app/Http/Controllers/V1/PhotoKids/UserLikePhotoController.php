<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserLikePhotoController extends Controller
{
    public function index(User $user):array
    {

        $mass = $user->likesPhoto()->get();
        $photo_id = array();

        foreach ($mass as $m){
            $photo_id[] = ["id" => $m->id];
        }

        return $photo_id;
    }
}

