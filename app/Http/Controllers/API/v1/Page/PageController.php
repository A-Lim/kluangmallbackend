<?php

namespace App\Http\Controllers\API\v1\Page;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Merchant;
use App\Repositories\Landing\ILandingRepository;
use App\Repositories\Merchant\IMerchantRepository;

class PageController extends ApiController {

    private $landingRepository;
    private $merchantRepository;

    public function __construct(ILandingRepository $iLandingRepository,
        IMerchantRepository $iMerchantRepository) {
        // $this->middleware('auth:api')->except(['landing', 'shops']);
        $this->landingRepository = $iLandingRepository;
        $this->merchantRepository = $iMerchantRepository;
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
}
