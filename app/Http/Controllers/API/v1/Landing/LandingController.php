<?php

namespace App\Http\Controllers\API\v1\Landing;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Repositories\Banner\IBannerRepository;
use App\Repositories\Event\IEventRepository;
use App\Repositories\Promotion\IPromotionRepository;

class LandingController extends ApiController {

    private $bannerRepository;
    private $eventRepository;
    private $promotionRepository;

    public function __construct(IBannerRepository $iBannerRepository,
        IEventRepository $iEventRepository, IPromotionRepository $iPromotionRepository) {
        $this->middleware('auth:api');
        $this->bannerRepository = $iBannerRepository;
        $this->eventRepository = $iEventRepository;
        $this->promotionRepository = $iPromotionRepository;
    }

    public function details(Request $request) {
        $data['banners'] = $this->bannerRepository->list(null, false);
        $data['events'] = $this->eventRepository->list(null, false);
        $data['promotions'] = $this->promotionRepository->list(null, false);
        return $this->responseWithData(200, $data);
    }
}
