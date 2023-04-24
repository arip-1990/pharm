<?php

namespace App\Http\Controllers\V1\Panel\Statistic;

use App\Http\Resources\StatisticResource;
use App\Models\VisitStatistic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection
    {
        $query = VisitStatistic::select('visit_statistics.*');
        if ($request->get('orderField')) {
            if ($request->get('orderField') === 'user') {
                $query->join('users', 'users.id', '=', 'visit_statistics.user_id')
                    ->orderBy('users.name', $request->get('orderDirection'));
            }
            else {
                $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
            }
        }
        else
            $query->orderByDesc('created_at');

        return StatisticResource::collection($query->paginate($request->get('pageSize', 10)));
    }
}
