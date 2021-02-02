<?php

namespace App\Http\Controllers\API\v1\Receipt;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Receipt;
use App\PointTransaction;
use App\Http\Requests\Receipt\UploadRequest;
use App\Repositories\Receipt\IReceiptRepository;
use App\Repositories\PointTransaction\IPointTransactionRepository;

class ReceiptController extends ApiController {

    private $receiptRepository;
    private $pointTransactionRepository;

    public function __construct(IReceiptRepository $iReceiptRepository,
        IPointTransactionRepository $iPointTransactionRepository) {
        $this->middleware('auth:api');
        $this->receiptRepository = $iReceiptRepository;
        $this->pointTransactionRepository = $iPointTransactionRepository;
    }

    public function upload(UploadRequest $request) {
        $user = auth()->user();

        // upload to other service

        $receipt = $this->receiptRepository->upload($user, $request->all(), $request->file('image'));

        $data = [
            'type' => PointTransaction::TYPE_ADD,
            'amount' => $receipt->points,
            'description' => 'Earned '.$receipt->points.' points from '.$receipt->merchant->name.'.'
        ];
        $this->pointTransactionRepository->create($user, $data, $receipt);

        return $this->responseWithMessage(200, 'Receipt upload successful.');
    }
}
