<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Http\JsonResponse;
use App\Interfaces\DashboardRepositoryInterface;

class DashboardController extends Controller
{
    protected $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function salesSummary(): JsonResponse
    {
        $summary = $this->dashboardRepository->getSalesSummary();
        return response()->json($summary);
    }

    public function dailySalesTrend(): JsonResponse
    {
        $trend = $this->dashboardRepository->getDailySalesTrend();
        return response()->json($trend);
    }
}
