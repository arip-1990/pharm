<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\Likes;
use App\Models\PhotoKids;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddLikeController extends Controller
{
    public function index(PhotoKids $photo)
    {
        $user_id = Auth::id();

        try {
            $like = Likes::query()->create([
                "photo_id" => $photo->id,
                "user_id" => "ee374378-12eb-ed11-80cc-001dd8b75065"
    //            "user_id" => $user_id,

            ]);
            $like->save();

        } catch (\Exception $e) {
            DB::table('likes')
                ->where('user_id', 'ee374378-12eb-ed11-80cc-001dd8b75065')
                ->where('photo_id', $photo->id)
                ->delete();
        }

    }

    public function myFavorite()
    {
        $user = Auth::user();
        $u = User::query()->where('id', 'ee374378-12eb-ed11-80cc-001dd8b75065')->with('likesPhoto')->get();
        return $u;
    }
}
