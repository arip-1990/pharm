<?php

namespace App\Http\Controllers\V3\PhotoKids;

use App\Http\Controllers\Controller;
use App\Models\PhotoKids;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function index(PhotoKids $id, Request $request){
        $photo = PhotoKids::query()->where('id', $id->id);
        $photo->delete();
        return $request->json(["massage" => "Success"], 200);
    }
}
