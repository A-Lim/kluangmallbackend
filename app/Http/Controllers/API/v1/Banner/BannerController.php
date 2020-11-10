<?php

namespace App\Http\Controllers\API\v1\Banner;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Banner;
use App\Repositories\Banner\IBannerRepository;

use App\Http\Requests\Banner\CreateRequest;
use App\Http\Requests\Banner\UpdateRequest;

class BannerController extends ApiController {

    private $bannerRepository;

    public function __construct(IBannerRepository $iBannerRepository) {
        $this->middleware('auth:api');
        $this->bannerRepository = $iBannerRepository;
    }

    public function list(Request $request) {
        // $this->authorize('viewAny', Banner::class);
        $banners = $this->bannerRepository->list($request->all(), true);
        return $this->responseWithData(200, $banners);
    }

    public function details(Banner $banner) {
        $this->authorize('view', $banner);
        return $this->responseWithData(200, $banner);
    }

    public function create(CreateRequest $request) {
        // $this->authorize('create', Banner::class);
        $banner = $this->bannerRepository->create($request->files->all());
        return $this->responseWithMessageAndData(201, $banner, 'Banner(s) created.');
    }

    public function activate(Banner $banner) {
        // $this->authorize('update', $banner);
        $data['status'] = Banner::STATUS_ACTIVE;
        $banner = $this->bannerRepository->update($banner, $data);
        return $this->responseWithMessageAndData(200, $banner, 'Banner activated.');
    }

    public function deactivate(Banner $banner) {
        // $this->authorize('update', $banner);
        $data['status'] = Banner::STATUS_INACTIVE;
        $banner = $this->bannerRepository->update($banner, $data);
        return $this->responseWithMessageAndData(200, $banner, 'Banner deactivated.');
    }

    public function delete(Banner $banner) {
        // $this->authorize('delete', $banner);
        $this->bannerRepository->delete($banner);
        return $this->responseWithMessage(200, 'Banner deleted.');
    }
}
