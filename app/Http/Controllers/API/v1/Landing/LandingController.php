<?php

namespace App\Http\Controllers\API\v1\Landing;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Repositories\Landing\ILandingRepository;
use App\Repositories\Banner\IBannerRepository;
use App\Repositories\Event\IEventRepository;
use App\Repositories\Promotion\IPromotionRepository;

class LandingController extends ApiController {

    private $landingRepository;
    private $bannerRepository;
    private $eventRepository;
    private $promotionRepository;

    public function __construct(ILandingRepository $iLandingRepository,
        IBannerRepository $iBannerRepository,
        IEventRepository $iEventRepository, IPromotionRepository $iPromotionRepository) {
        $this->middleware('auth:api');
        $this->landingRepository = $iLandingRepository;
    }

    public function details(Request $request) {
        // $this->authorize('viewAny', Landing::class);
        $data = $this->landingRepository->list();
        return $this->responseWithData(200, $data);
    }

    public function update(Request $request) {
        $this->authorize('update', null);
        $this->landingRepository->update($request->all());
        return $this->responseWithMessage(200, 'Landing Page updated.');
    }
}
