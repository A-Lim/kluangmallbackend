<?php

namespace App\Http\Controllers\API\v1\ApiLog;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\ApiLog;
use App\Repositories\ApiLog\IApiLogRepository;

class ApiLogController extends ApiController {

    private $apiLogRepository;

    public function __construct(IApiLogRepository $iApiLogRepository) {
        $this->middleware('auth:api');
        $this->apiLogRepository = $iApiLogRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', ApiLog::class);
        $apilogs = $this->apiLogRepository->list($request->all(), true);
        return $this->responseWithData(200, $apilogs);
    }
}
