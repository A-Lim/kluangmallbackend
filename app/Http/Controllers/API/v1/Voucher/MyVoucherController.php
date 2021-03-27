<?php

namespace App\Http\Controllers\API\v1\Voucher;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Repositories\Voucher\IMyVoucherRepository;
use App\Repositories\Voucher\IVoucherRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\Merchant\IMerchantRepository;

use App\MyVoucher;

use App\Http\Requests\Voucher\UserScanMerchantRequest;
use App\Http\Requests\Voucher\MerchantScanUserRequest;
use App\Notifications\Voucher\VoucherUsed;

use App\Http\Resources\Vouchers\MyVoucherResource;
use App\Http\Resources\Vouchers\MyVoucherCollection;

class MyVoucherController extends ApiController {

    private $userRepository;
    private $merchantRepository;
    private $voucherRepository;
    private $myVoucherRepository;

    public function __construct(IUserRepository $iUserRepository,
        IMerchantRepository $iMerchantRepository,
        IVoucherRepository $iVoucherRepository,
        IMyVoucherRepository $iMyVoucherRepository) {
        $this->middleware('auth:api');
        $this->userRepository = $iUserRepository;
        $this->merchantRepository = $iMerchantRepository;
        $this->voucherRepository = $iVoucherRepository;
        $this->myVoucherRepository = $iMyVoucherRepository;
    }

    public function list(Request $request) {
        $myVouchers = $this->myVoucherRepository->list($request->all(), true);
        return $this->responseWithData(200, new MyVoucherCollection($myVouchers));
    }

    public function listMyActive(Request $request) {
        $user = auth()->user();
        $myVouchers = $this->myVoucherRepository->listActive($user, true);
        return $this->responseWithData(200, new MyVoucherCollection($myVouchers));
    }

    public function listMyInactive(Request $request) {
        $user = auth()->user();
        $myVouchers = $this->myVoucherRepository->listInactive($user, true);
        return $this->responseWithData(200, new MyVoucherCollection($myVouchers));
    }

    public function details(Request $request, MyVoucher $myVoucher) {
        $user = auth()->user();
        if ($myVoucher->user_id != $user->id)
            return $this->responseWithMessage(400, 'This voucher does not belong to you.');

        $myVoucher = $this->myVoucherRepository->details($myVoucher->id);
        return $this->responseWithData(200, new MyVoucherResource($myVoucher));
    }

    // swipe to use
    public function swipetouse(Request $request, MyVoucher $myVoucher) {
        $user = auth()->user();

        if ($myVoucher->voucher->merchants->count() == 0)
            return $this->responseWithMessage(400, 'This voucher is invalid.');

        if ($myVoucher->voucher->merchants->count() > 1)
            return $this->responseWithMessage(400, 'This voucher is not redeemable this way.');

        if ($myVoucher->user_id != $user->id)
            return $this->responseWithMessage(400, 'This voucher does not belong to you.');

        if ($myVoucher->status != MyVoucher::STATUS_ACTIVE)
            return $this->responseWithMessage(400, 'This voucher is invalid.'); 

        $this->myVoucherRepository->use($user, $myVoucher);
        return $this->responseWithMessage(200, 'Voucher successfully used.');
    }

    // user scan merchant
    public function userscanmerchant(UserScanMerchantRequest $request, MyVoucher $myVoucher) {
        $user = auth()->user();
        $merchant = $this->merchantRepository->find($request->merchant_id);

        if ($merchant == null || $myVoucher->status != MyVoucher::STATUS_ACTIVE)
            return $this->responseWithMessage(400, 'This voucher is invalid.');

        $voucher = $this->voucherRepository->find($myVoucher->voucher_id);

        if ($voucher->data != $request->data)
            return $this->responseWithMessage(400, 'This voucher is invalid.');

        $this->myVoucherRepository->use($user, $myVoucher, $merchant);
        $user->notify(new VoucherUsed($myVoucher, $merchant));
        return $this->responseWithMessage(200, 'Voucher successfully used.');
    }

    // merchant scan user
    public function merchantscanuser(MerchantScanUserRequest $request, MyVoucher $myVoucher) {
        $merchant = auth()->user()->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        if ($myVoucher->status != MyVoucher::STATUS_ACTIVE)
            return $this->responseWithMessage(400, 'This voucher is invalid.'); 

        $user = $this->userRepository->find($request->user_id);
        $this->myVoucherRepository->use($user, $myVoucher, $merchant);

        $user->notify(new VoucherUsed($myVoucher, $merchant));
        return $this->responseWithMessage(200, 'Voucher successfully used.');
    }
}