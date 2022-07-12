<?php

namespace App\Http\Controllers\Store;

use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): JsonResource
    {
            return StoreResource::collection(Store::query()->paginate(15));
    }
}
