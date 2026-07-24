<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    private WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function show(?int $branchId = null, Request $request): JsonResponse
    {
        $id = $branchId ?: (int) $request->get('branch_id', 0);
        $data = $this->weatherService->getBranchWeather($id ?: null);
        return response()->json(['data' => $data]);
    }
}
