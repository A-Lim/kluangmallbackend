<?php

namespace App\Http\Controllers\API\v1\Point;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\PointTransaction;
use App\Repositories\PointTransaction\IPointTransactionRepository;

class PointController extends ApiController {

    private $pointTransactionRepository;

    public function __construct(IPointTransactionRepository $iPointTransactionRepository) {
        $this->middleware('auth:api');
        $this->pointTransactionRepository = $iPointTransactionRepository;
    }

    public function list(Request $request) {
        $receipts = $this->pointTransactionRepository->list($request->all(), true);
        return $this->responseWithData(200, $receipts);
    }

    public function listMy(Request $request) {
        $user = auth()->user();
        $data = $request->all();
        $data['user_id'] = 'equals:'.$user->id;
        $receipts = $this->pointTransactionRepository->list($data, true);
        return $this->responseWithData(200, $receipts);
    }
}
