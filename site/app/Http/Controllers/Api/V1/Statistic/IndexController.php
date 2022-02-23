<?php

namespace App\Http\Controllers\Api\V1\Statistic;

use App\Repositories\StatisticRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    public function __construct(private StatisticRepository $statisticRepository) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $statistics = $this->statisticRepository->getAll($request);
        }
        catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

        return response()->json($statistics);
    }
}
