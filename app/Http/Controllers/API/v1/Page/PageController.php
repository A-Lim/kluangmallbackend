<?php

namespace App\Http\Controllers\API\v1\Page;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Merchant;
use App\Repositories\Landing\ILandingRepository;
use App\Repositories\Merchant\IMerchantRepository;
use App\Repositories\SystemSetting\ISystemSettingRepository;
use App\Repositories\Voucher\IVoucherTransactionRepository;

class PageController extends ApiController {

    private $landingRepository;
    private $merchantRepository;
    private $systemSettingRepository;
    private $voucherTransactionRepository;

    public function __construct(ILandingRepository $iLandingRepository,
        IMerchantRepository $iMerchantRepository,
        ISystemSettingRepository $iSystemSettingRepository,
        IVoucherTransactionRepository $iVoucherTransactionRepository) {
        $this->middleware('auth:api')->only(['merchantLanding']);
        $this->landingRepository = $iLandingRepository;
        $this->merchantRepository = $iMerchantRepository;
        $this->systemSettingRepository = $iSystemSettingRepository;
        $this->voucherTransactionRepository = $iVoucherTransactionRepository;
    }

    public function userLanding(Request $request) {
        $data = $this->landingRepository->list('user');
        return $this->responseWithData(200, $data);
    }

    public function merchantLanding(Request $reqeust) {
        $user = auth()->user();
        $merchant = $user->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        $data = $this->landingRepository->list('merchant');
        $data['unique_visits'] = $this->merchantRepository->visitCount($merchant);
        $data['vouchers_redeemed'] = $this->voucherTransactionRepository->redeemCount($merchant);
        $data['points_given'] = 0;
        return $this->responseWithData(200, $data);
    }

    public function shops(Request $request) {
        $merchants = $this->merchantRepository->listAllShops();
        return $this->responseWithData(200, $merchants);
    }

    public function shopDetail(Request $request, Merchant $merchant) {
        $similar = $this->merchantRepository->listSimilar($merchant);
        $data = [
            'details' => $this->merchantRepository->find($merchant->id, false),
            'similar' => $similar,
        ];
        return $this->responseWithData(200, $data);
    }

    public function aboutUs(Request $request) {
        $aboutUsSetting = $this->systemSettingRepository->findByCode('about_us');
        return $this->responseWithData(200, $aboutUsSetting->value);
    }

    public function contactUs(Request $request) {
        $data = $this->systemSettingRepository->findByCodes(['email', 'phone', 'address']);
        $result = [];
        foreach ($data as $systemsetting) {
            $result[$systemsetting->code] = $systemsetting->value;
        }
        return $this->responseWithData(200, $result);
    }
    
    public function termsAndConditions(Request $request) {
        $aboutUsSetting = $this->systemSettingRepository->findByCode('terms_and_conditions');
        return $this->responseWithData(200, $aboutUsSetting->value);
    }

    public function privacyPolicy(Request $request) {
        $aboutUsSetting = $this->systemSettingRepository->findByCode('privacy_policy');
        return $this->responseWithData(200, $aboutUsSetting->value);
    }
}
