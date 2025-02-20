<?php

namespace App\Http\Controllers\Chart;

use App\Http\Controllers\Controller;
use App\Services\Chart\ProducerDashboardService;
use Illuminate\Http\Request;

class ProducerDashboardController extends Controller
{
    public function __construct(
        protected ProducerDashboardService $producerDashboardService
    ) {}

    public function index() {
        return $this->producerDashboardService->get();
    }
}
