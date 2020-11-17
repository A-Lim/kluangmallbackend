<?php

namespace App\Http\Controllers\API\v1\Page;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Repositories\Landing\ILandingRepository;

class PageController extends ApiController {

    private $landingRepository;

    public function __construct(ILandingRepository $iLandingRepository) {
        $this->middleware('auth:api')->except(['landing']);
        $this->landingRepository = $iLandingRepository;
    }

    public function landing(Request $request) {
        $data = $this->landingRepository->list();
        return $this->responseWithData(200, $data);
    }
}
