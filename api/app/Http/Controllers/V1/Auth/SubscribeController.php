<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Faker\Core\Uuid;
use Illuminate\Http\Request;
use SebastianBergmann\Type\TrueType;

class SubscribeController extends Controller
{
    public function index(Request $request)
    {
        $phone = $request->input('phone');
        $subscribeSms = $request->boolean('subscribeSms');
        $subscribeEmail = $request->boolean('subscribeEmail');

        $user = User::query()->where("phone", $phone)->first();

        if ($subscribeSms){
            $user->SMS_distribution = True;
        }
        if ($subscribeEmail){
            $user->Email_distribution = True;
        }
        $user->save();

    }
}
