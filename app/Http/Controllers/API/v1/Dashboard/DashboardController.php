<?php

namespace App\Http\Controllers\API\v1\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Repositories\Dashboard\IDashboardRepository;

class DashboardController extends ApiController {

    private $dasboardRepository;

    public function __construct(IDashboardRepository $iDashboardRepository) {
        $this->middleware('auth:api');
        $this->dasboardRepository = $iDashboardRepository;
    }

    public function stats(Request $request) {
        $stats = $this->dasboardRepository->stats();
        return $this->responseWithData(200, $stats);
    }

    public function top_10_merchant_visits(Request $request) {
        $data = $this->dasboardRepository->top_merchants_visit();
        return $this->responseWithData(200, $data);
    }

    public function new_users(Request $request) {
        $data = $this->dasboardRepository->new_users($request->all());
        return $this->responseWithData(200, $data);
    }
}
