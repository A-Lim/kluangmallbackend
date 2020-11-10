<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\ApiLog;
use App\Repositories\ApiLog\IApiLogRepository;

class ApiLogger {
    private $apiLogRepository;

    public function __construct(IApiLogRepository $iApiLogRepository) {
        $this->apiLogRepository = $iApiLogRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        return $next($request);
    }

    public function terminate(Request $request, $response) {
        $this->apiLogRepository->create($request, $response);
    }
}
