<?php

namespace App\Http\Controllers\Api\V1\Statistic;

use App\Entities\VisitStatistic;
use App\Http\Resources\StatisticResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = VisitStatistic::query();
        if ($request->get('orderField'))
            $query->orderBy($request->get('orderField'), $request->get('orderDirection'));

        return StatisticResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
