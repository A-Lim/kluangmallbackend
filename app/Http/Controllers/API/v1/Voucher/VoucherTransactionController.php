<?php

namespace App\Http\Controllers\API\v1\Voucher;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\User;
use App\Voucher;
use App\VoucherLimit;
use App\Repositories\Voucher\IVoucherTransactionRepository;


class VoucherTransactionController extends ApiController {

    private $transactionRepository;

    public function __construct(IVoucherTransactionRepository $iVoucherTransactionRepository) {
        $this->middleware('auth:api');
        $this->transactionRepository = $iVoucherTransactionRepository;
    }

    public function list(Request $request) {
        $transactions = $this->transactionRepository->list($request->all(), true);
        return $this->responseWithData(200, $transactions);
    }

    public function listMy(Request $request) {
        $user = auth()->user();
        $transactions = $this->transactionRepository->listMy($user, true);
        return $this->responseWithData(200, $transactions);
    }
}