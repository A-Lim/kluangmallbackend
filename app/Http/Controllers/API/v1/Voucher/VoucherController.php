<?php

namespace App\Http\Controllers\API\v1\Voucher;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\User;
use App\Voucher;
use App\VoucherLimit;
use App\Repositories\Voucher\IVoucherRepository;

use App\Http\Requests\Voucher\CreateRequest;
use App\Http\Requests\Voucher\UpdateRequest;

class VoucherController extends ApiController {

    private $voucherRepository;

    public function __construct(IVoucherRepository $iVoucherRepository) {
        $this->middleware('auth:api');
        $this->voucherRepository = $iVoucherRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', Voucher::class);
        $vouchers = $this->voucherRepository->list($request->all(), true);
        return $this->responseWithData(200, $vouchers);
    }

    public function details(Voucher $voucher) {
        // $this->authorize('view', $voucher);
        $voucher = $this->voucherRepository->find($voucher->id);
        return $this->responseWithData(200, $voucher);
    }

    public function create(CreateRequest $request) {
        // $this->authorize('create', Voucher::class);
        $voucher = $this->voucherRepository->create($request->all());

        return $this->responseWithMessageAndData(201, $voucher, 'Voucher created.');
    }

    public function update(UpdateRequest $request, Voucher $voucher) {
        // $this->authorize('update', $voucher);
        $voucher = $this->voucherRepository->update($voucher, $request->all());
        return $this->responseWithMessageAndData(200, $voucher, 'Voucher updated.'); 
    }

    public function redeem(Request $request, Voucher $voucher) {
        $user = auth()->user();

        $result = $this->validateVoucher($user, $voucher);

        // validation fail
        if ($result != null && get_class($result) == 'Illuminate\Http\JsonResponse')
            return $result;
        
        // redeem voucher
        $this->voucherRepository->redeem($voucher, $user);
        return $this->responseWithMessage(200, 'Voucher redeemed.');
    }

    public function used(Request $request, Voucher $voucher) {

    }

    public function validateVoucher(User $user, Voucher $voucher) {
        $today = Carbon::today();
        // check if enough points
        if ($user->points < $voucher->points)
            return $this->responseWithMessage(400, 'Insufficient points to redeem voucher.');

        // check if period is valid
        if ($today->lessThan($voucher->fromDate)) 
            return $this->responseWithMessage(400, 'Voucher period has not started.');

        if ($today->greaterThan($voucher->toDate))
            return $this->responseWithMessage(400, 'Voucher has expired.');

        if (!$voucher->has_redemption_limit)
            return;

        // person limit
        $personLimit = $voucher->limits->filter(function ($limit) {
            return $limit->type == VoucherLimit::TYPE_PERSON;
        })->first();

        if ($personLimit && $this->voucherRepository->hasReachedPersonLimit($user, $personLimit))
            return $this->responseWithMessage(400, 'You have reach the limit for redeeming this voucher.');

        // perday limit
        $perDayLimit = $voucher->limits->filter(function ($limit) {
            return $limit->type == VoucherLimit::TYPE_PERDAY;
        })->first();

        if ($perDayLimit && $this->voucherRepository->hasReachedPerDayLimit($user, $perDayLimit))
            return $this->responseWithMessage(400, 'You have reach the limit for redeeming this voucher today.');

        // daily limit
        $dailyLimit = $voucher->limits->filter(function ($limit) {
            return $limit->type == VoucherLimit::TYPE_DAILY;
        })->first();

        if ($dailyLimit && $this->voucherRepository->hasReachedDailyLimit($dailyLimit))
            return $this->responseWithMessage(400, 'Voucher daily limit reached.');

        // count limit
        $totalLimit = $voucher->limits->filter(function ($limit) {
            return $limit->type == VoucherLimit::TYPE_TOTAL;
        })->first();

        if ($totalLimit && $this->voucherRepository->hasReachedTotalLimit($totalLimit))
            return $this->responseWithMessage(400, 'Voucher total limit reached.');

    }

}