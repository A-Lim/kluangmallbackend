<?php

namespace App\Http\Controllers\API\v1\Voucher;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\User;
use App\Voucher;
use App\VoucherLimit;
use App\PointTransaction;
use App\Repositories\Voucher\IVoucherRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\PointTransaction\IPointTransactionRepository;

use App\Http\Requests\Voucher\CreateRequest;
use App\Http\Requests\Voucher\UpdateRequest;
use App\Http\Requests\Voucher\UseUserVoucher;

use App\Notifications\Voucher\VoucherUsed;

use Libern\QRCodeReader\QRCodeReader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VoucherController extends ApiController {

    private $voucherRepository;
    private $userRepository;
    private $pointTransactionRepository;

    public function __construct(IVoucherRepository $iVoucherRepository,
        IUserRepository $iUserRepository,
        IPointTransactionRepository $iPointTransactionRepository) {
        $this->middleware('auth:api');
        $this->voucherRepository = $iVoucherRepository;
        $this->userRepository = $iUserRepository;
        $this->pointTransactionRepository = $iPointTransactionRepository;
    }

    public function list(Request $request) {
        $this->authorize('viewAny', Voucher::class);
        $vouchers = $this->voucherRepository->list($request->all(), true);
        return $this->responseWithData(200, $vouchers);
    }

    // list vouchers that are redeemable by users
    public function listRewards(Request $request) {
        $vouchers = $this->voucherRepository->listAvailable($request->all(), true);
        return $this->responseWithData(200, $vouchers);
    }

    // reward details
    public function rewardDetails(Request $request, Voucher $voucher) {
        $reward = $this->voucherRepository->rewardDetail($voucher);
        return $this->responseWithData(200, $reward);
    }

    // list merchants active vouchers
    public function listMerchantsActive(Request $request) {
        $merchant = auth()->user()->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        $vouchers = $this->voucherRepository->listMerchantsActive($merchant, true);
        return $this->responseWithData(200, $vouchers);
    }

    // list merchants ended vouchers
    public function listMerchantsInactive(Request $request) {
        $merchant = auth()->user()->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        $vouchers = $this->voucherRepository->listMerchantsInactive($merchant, true);
        return $this->responseWithData(200, $vouchers);
    }

    public function listMyActive(Request $request) {
        $user = auth()->user();
        $vouchers = $this->voucherRepository->listMyActive($user, true);
        return $this->responseWithData(200, $vouchers);
    }

    public function listMyInactive(Request $request) {
        $user = auth()->user();
        $vouchers = $this->voucherRepository->listMyExpiredUsed($user, true);
        return $this->responseWithData(200, $vouchers);
    }

    public function myVoucher(Voucher $voucher) {
        $user = auth()->user();
        $voucher = $this->voucherRepository->findMyVoucher($user, $voucher);

        if (!$voucher)
            return $this->responseWithMessage(400, 'Voucher not found.');

        return $this->responseWithData(200, $voucher);
    }

    public function details(Voucher $voucher) {
        // $this->authorize('view', $voucher);
        $voucher = $this->voucherRepository->find($voucher->id);
        return $this->responseWithData(200, $voucher);
    }

    public function create(CreateRequest $request) {
        $this->authorize('create', Voucher::class);
        $data = $request->all();
        if ($request->hasFile('uploadQr')) {
            $decoded = $this->decodeQr($request->file('uploadQr'));
            if (!$decoded)
                return $this->responseWithMessage(400, 'Invalid Qr Code.');

            $data['data'] = $decoded;
        }

        $voucher = $this->voucherRepository->create($data, $request->files->all());
        return $this->responseWithMessageAndData(201, $voucher, 'Voucher created.');
    }

    public function update(UpdateRequest $request, Voucher $voucher) {
        $this->authorize('update', $voucher);
        $data = $request->all();
        if ($request->hasFile('uploadQr')) {
            $decoded = $this->decodeQr($request->file('uploadQr'));
            if (!$decoded)
                return $this->responseWithMessage(400, 'Invalid Qr Code.');

            $data['data'] = $decoded;
        }

        $voucher = $this->voucherRepository->update($voucher, $data, $request->files->all());
        return $this->responseWithMessageAndData(200, $voucher, 'Voucher updated.'); 
    }

    public function delete(Request $request, Voucher $voucher) {
        $this->authorize('delete', $voucher);
        $this->voucherRepository->delete($voucher);
        return $this->responseWithMessage(200, 'Voucher deleted.');
    }

    public function redeem(Request $request, Voucher $voucher) {
        $user = auth()->user();
        $result = $this->validateVoucher($user, $voucher);

        // validation fail
        if ($result != null && get_class($result) == 'Illuminate\Http\JsonResponse')
            return $result;
        
        // redeem voucher
        $this->voucherRepository->redeem($voucher, $user);
        $data = [
            'type' => PointTransaction::TYPE_DEDUCT,
            'amount' => $voucher->points,
            'description' => 'Deducted '.$voucher->points.' points from redeeming voucher '.$voucher->name.'.'
        ];
        $this->pointTransactionRepository->create($user, $data);
        return $this->responseWithMessage(200, 'Voucher redeemed.');
    }

    public function use(Request $request, Voucher $voucher) {
        $user = auth()->user();
        $result = $this->validateMyVoucher($user, $voucher);

        // validation fail
        if ($result != null && get_class($result) == 'Illuminate\Http\JsonResponse')
            return $result;
        
        // consume voucher
        $this->voucherRepository->use($voucher, $user);
        return $this->responseWithMessage(200, 'Voucher used.');
    }

    // merchant scan user to redeem voucher
    public function useOnBehalfOfUser(UseUserVoucher $request) {
        $merchant = auth()->user()->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        $voucher = $this->voucherRepository->find($request->voucher_id);
        $user = $this->userRepository->find($request->user_id);
        $result = $this->validateMyVoucher($user, $voucher);

        // validation fail
        if ($result != null && get_class($result) == 'Illuminate\Http\JsonResponse')
            return $result;

        // consume voucher
        $myVoucher = $this->voucherRepository->use($voucher, $user);

        // send notification
        $user->notify(new VoucherUsed($myVoucher));

        return $this->responseWithMessage(200, 'Voucher used.');
    }

    public function validateVoucher(User $user, Voucher $voucher) {
        $today = Carbon::today();
        // check if enough points
        if ($user->points < $voucher->points)
            return $this->responseWithMessage(400, 'Insufficient points to redeem voucher.');

        if ($voucher->status != Voucher::STATUS_ACTIVE)
            return $this->responseWithMessage(400, 'Voucher is no longer valid.');

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

    public function validateMyVoucher(User $user, Voucher $voucher, $isMerchant = false) {
        $today = Carbon::today();
        $myVouchers = $this->voucherRepository->listMyActive($user);

        if ($myVouchers->count() == 0) {
            $message = null;
            if ($isMerchant)
                $message = 'User does not own this voucher.';
            else 
                $message = 'You do not own this voucher.';

            return $this->responseWithMessage(400, $message);
        }
             

        if ($voucher->status != Voucher::STATUS_ACTIVE)
            return $this->responseWithMessage(400, 'Voucher is no longer valid.');
    }

    private function decodeQr(UploadedFile $file) {
        $qrCodeReader = new QRCodeReader();
        return $qrCodeReader->decode($file->getPathName());
    }
}