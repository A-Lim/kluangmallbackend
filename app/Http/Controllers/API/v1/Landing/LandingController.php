<?php

namespace App\Http\Controllers\API\v1\Landing;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Repositories\Landing\ILandingRepository;
use App\Repositories\Banner\IBannerRepository;
use App\Repositories\Event\IEventRepository;
use App\Repositories\Promotion\IPromotionRepository;

use App\Http\Requests\Landing\UpdateRequest;

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

    public function details(Request $request, $app) {
        switch ($app) {
            case 'user':
                $this->authorize('userViewAny', Landing::class);
                break;
            
            case 'merchant':
                $this->authorize('merchantViewAny', Landing::class);
                break;
            
            default:
                return $this->responseWithMessage(400, 'Invalid app type');
        }
        
        $data = $this->landingRepository->list($app);
        return $this->responseWithData(200, $data);
    }

    public function update(UpdateRequest $request) {
        switch ($request->app) {
            case 'user':
                $this->authorize('userUpdate', Landing::class);
                break;
            
            case 'merchant':
                $this->authorize('merchantUpdate', Landing::class);
                break;
            
            default:
                return $this->responseWithMessage(400, 'Invalid app type');
        }
        $this->landingRepository->update($request->all());
        return $this->responseWithMessage(200, 'Landing Page updated.');
    }
}
