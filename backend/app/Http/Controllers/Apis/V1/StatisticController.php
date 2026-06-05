<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Services\StatisticService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    use ResponseHelper;

    public function __construct(
        private StatisticService $statisticService
    ) {
    }

    public function dashboard(Request $request)
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'granularity' => ['nullable', 'in:day,month'],
        ]);

        return $this->success($this->statisticService->dashboard($filters));
    }
}
