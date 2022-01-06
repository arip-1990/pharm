<?php

namespace App\Repositories;

use App\Entities\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StatisticRepository
{
    public function getAll(Request $request): Collection
    {
        $current = (int)$request->get('page', 1);
        $pageSize = (int)$request->get('pageSize', 10);
        $query = Statistic::query();

        if ($request->get('orderField'))
            $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
        
        $total = $query->count();
        $statistics = $query->skip(($current - 1) * $pageSize)->take($pageSize)->get()->map(function (Statistic $statistic) {
            return [
                'id' => $statistic->id,
                'ip' => $statistic->ip,
                'city' => $statistic->city,
                'os' => $statistic->os,
                'browser' => $statistic->browser,
                'screen' => $statistic->screen,
                'referrer' => $statistic->referrer,
                'createdAt' => $statistic->created_at,
                'updatedAt' => $statistic->updated_at,
            ];
        });

        return new Collection([
            'current' => $current,
            'pageSize' => $pageSize,
            'total' => $total,
            'data' => $statistics
        ]);
    }
}
