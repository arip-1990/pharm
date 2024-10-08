<?php

namespace App\Http\Controllers\V3\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\PhotoKids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    public function index(Request $request){

        $ids = $request->input('ids');
        $photos = PhotoKids::query()->whereIn("id", $ids)->get();
        foreach ($photos as $photo) {
            $photo->published = true;
            $photo->save();
        }
        return $request->json(["message", "Success"], 200);
    }
}
