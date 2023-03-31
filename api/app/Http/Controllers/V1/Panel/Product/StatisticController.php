<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Resources\ProductStatisticResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class StatisticController extends Controller
{
    public function __invoke(Request $request): ResourceCollection
    {
        return ProductStatisticResource::collection(
            User::whereHas('addPhotos')->orWhereHas('editProducts')->orWhereHas('editValues')
                ->paginate($request->get('pageSize', 10))
        );
    }
}
