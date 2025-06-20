<?php

namespace App\Interfaces;

interface DashboardRepositoryInterface
{
    public function getSalesSummary();
    public function getDailySalesTrend();
}
