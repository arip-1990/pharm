<?php

namespace App\Http\Controllers\V1\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\PhotoKids;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AddPhotoController extends Controller
{

    private function date_in_category($data, $today_data): int
    {
        [$year, $mouth, $day] = $data;
        [$today_year, $today_mouth, $today_day] = $today_data;
        $age = (int)$today_year - (int)$year;

        if ((int)$mouth > (int)$today_mouth) {
            $age -= 1;
        } else if ( ((int)$mouth == (int)$today_mouth) && ((int)$day > (int)$today_day) ) {
            $age -= 1;
        }

        if (3 <= $age && $age <= 5) {
            return 1;
        }else if (6 <= $age && $age <= 8) {
            return 2;
        }else if (9 <= $age && $age <= 11) {
            return 3;
        }else if (12 <= $age && $age <= 14){
            return 4;
        }else {
            return 0;
        }

    }

    public function index(Request $request): JsonResponse
    {

        $data = explode("-", $request->get('birthdate'));
        $today_data = explode("-", date('Y-m-d'));;
        $categoryAge = $this->date_in_category($data, $today_data);

        if (!$categoryAge){
            return response()->json(["error" => "Вы не попадаете в возрастную категорию"], 400);
        }

        $file = $request->file('file');
        $path = Storage::disk('s3')->putFile('KidsPhotos', $file);
//        $path = Storage::disk('s3')->putFile('Test', $file);

        $url_key = Storage::disk('s3')->url($path);
        $url = explode("=", $url_key);

        $photo = new PhotoKids([
            "link" => array_pop($url),
            "photo_name" => $request->get("photo_name"),
            "birthdate" => $request->get("birthdate"),
            "first_name" => $request->get('first_name'),
            "last_name" => $request->get('last_name'),
            "middle_name" => $request->get('middle_name'),
            "user_id" => $request->get("user_id"),
            "age_category_id" => $categoryAge
        ]);

        $photo->save();

        return response()->json(["message" => "Success"]);
    }

}
