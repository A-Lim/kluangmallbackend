<?php

namespace App\Http\Controllers\API\v1\Page;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Merchant;
use App\Repositories\Landing\ILandingRepository;
use App\Repositories\Merchant\IMerchantRepository;
use App\Repositories\SystemSetting\ISystemSettingRepository;

class PageController extends ApiController {

    private $landingRepository;
    private $merchantRepository;
    private $systemSettingRepository;

    public function __construct(ILandingRepository $iLandingRepository,
        IMerchantRepository $iMerchantRepository,
        ISystemSettingRepository $iSystemSettingRepository) {
        // $this->middleware('auth:api')->except(['landing', 'shops']);
        $this->landingRepository = $iLandingRepository;
        $this->merchantRepository = $iMerchantRepository;
        $this->systemSettingRepository = $iSystemSettingRepository;
    }

    public function landing(Request $request) {
        $data = $this->landingRepository->list();
        return $this->responseWithData(200, $data);
    }

    public function shops(Request $request) {
        $merchants = $this->merchantRepository->list($request->all(), false);
        return $this->responseWithData(200, $merchants);
    }

    public function shopDetail(Request $request, Merchant $merchant) {
        $similar = $this->merchantRepository->listSimilar($merchant);
        $data = [
            'details' => $merchant,
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
            $result[$systemsetting->name] = $systemsetting->value;
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
