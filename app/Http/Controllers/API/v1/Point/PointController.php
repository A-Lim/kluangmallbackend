<?php

namespace App\Http\Controllers\API\v1\Point;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\PointTransaction;
use App\Http\Requests\Point\AddDeductPointsRequest;
use App\Repositories\User\IUserRepository;
use App\Repositories\PointTransaction\IPointTransactionRepository;

class PointController extends ApiController {

    private $userRepository;
    private $pointTransactionRepository;

    public function __construct(IUserRepository $iUserRepository,
        IPointTransactionRepository $iPointTransactionRepository) {
        $this->middleware('auth:api');
        $this->userRepository = $iUserRepository;
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

    public function addDeductPoints(AddDeductPointsRequest $request) {
        $user = $this->userRepository->find($request->user_id);
        $this->authorize('update', $user);
        $points = $this->pointTransactionRepository->create($user, $request->all());
        return $this->responseWithMessageAndData(200, ['points' => $points], 'Points '.$request->type.'ed.');
    }
}
