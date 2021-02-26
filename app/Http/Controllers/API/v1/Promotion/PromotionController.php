<?php

namespace App\Http\Controllers\API\v1\Promotion;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Promotion;
use App\Banner;
use App\Repositories\Promotion\IPromotionRepository;
use App\Repositories\Banner\IBannerRepository;

use App\Http\Requests\Promotion\CreateRequest;
use App\Http\Requests\Promotion\UpdateRequest;

class PromotionController extends ApiController {

    private $promotionRepository;
    private $bannerRepository;

    public function __construct(IPromotionRepository $iPromotionRepository,
        IBannerRepository $iBannerRepository) {
        $this->middleware('auth:api')->except(['list', 'details']);
        $this->promotionRepository = $iPromotionRepository;
        $this->bannerRepository = $iBannerRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', Promotion::class);
        $promotions = $this->promotionRepository->list($request->all(), true);
        return $this->responseWithData(200, $promotions);
    }

    public function details(Promotion $promotion) {
        // $this->authorize('view', $promotion);
        return $this->responseWithData(200, $promotion);
    }

    public function create(CreateRequest $request) {
        $this->authorize('create', Promotion::class);
        $promotion = $this->promotionRepository->create($request->all(), $request->files->all());
        return $this->responseWithMessageAndData(201, $promotion, 'Promotion created.');
    }

    public function update(UpdateRequest $request, Promotion $promotion) {
        $this->authorize('update', $promotion);
        $promotion = $this->promotionRepository->update($promotion, $request->all(), $request->files->all());
        return $this->responseWithMessageAndData(200, $promotion, 'Promotion updated.');
    }

    public function delete(Promotion $promotion) {
        $this->authorize('delete', $promotion);
        $this->promotionRepository->delete($promotion);
        $this->bannerRepository->removeIsClickable(Banner::TYPE_PROMOTION, $promotion->id);
        return $this->responseWithMessage(200, 'Promotion deleted.');
    }
}
