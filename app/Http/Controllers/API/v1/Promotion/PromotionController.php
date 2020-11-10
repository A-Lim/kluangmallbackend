<?php

namespace App\Http\Controllers\API\v1\Promotion;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Promotion;
use App\Repositories\Promotion\IPromotionRepository;

use App\Http\Requests\Promotion\CreateRequest;
use App\Http\Requests\Promotion\UpdateRequest;

class PromotionController extends ApiController {

    private $promotionRepository;

    public function __construct(IPromotionRepository $iPromotionRepository) {
        $this->middleware('auth:api');
        $this->promotionRepository = $iPromotionRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', Promotion::class);
        $promotions = $this->promotionRepository->list($request->all(), true);
        return $this->responseWithData(200, $promotions);
    }

    public function details(Promotion $promotion) {
        $this->authorize('view', $promotion);
        return $this->responseWithData(200, $promotion);
    }

    public function create(CreateRequest $request) {
        // $this->authorize('create', Promotion::class);
        $promotion = $this->promotionRepository->create($request->all(), $request->files->all());
        return $this->responseWithMessageAndData(201, $promotion, 'Promotion created.');
    }

    public function update(UpdateRequest $request, Promotion $promotion) {
        // $this->authorize('update', $promotion);
        $promotion = $this->promotionRepository->update($promotion, $request->all(), $request->files->all());
        return $this->responseWithMessageAndData(200, $promotion, 'Promotion updated.');
    }

    public function delete(Promotion $promotion) {
        // $this->authorize('delete', $promotion);
        $this->promotionRepository->delete($promotion);
        return $this->responseWithMessage(200, 'Promotion deleted.');
    }
}
