<?php
namespace App\Repositories\ApiLog;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface IApiLogRepository {
    /**
     * List all api logs
     * @param array $data
     * @param bool $paginate = false
     * @return array [ApiLog]
     */
    public function list($data, $paginate = false);


    /**
     * Create an api log
     * @param Illuminate\Http\Request
     * @param Illuminate\Http\JsonResponse
     * @return ApiLog
     */
    public function create(Request $request, JsonResponse $response);

    /**
     * Clear old api logs
     * @param integer days
     * @return null
     */
    public function clear_old_logs($days);
}