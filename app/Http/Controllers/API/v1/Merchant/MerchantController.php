<?php

namespace App\Http\Controllers\API\v1\Merchant;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use App\Merchant;
use App\MerchantCategory;
use App\Repositories\Merchant\IMerchantRepository;
use App\Repositories\User\IUserRepository;

use App\Http\Requests\Merchant\TrackRequest;
use App\Http\Requests\Merchant\CreateRequest;
use App\Http\Requests\Merchant\UpdateRequest;
use App\Http\Requests\Merchant\CreateUsersRequest;


class MerchantController extends ApiController {

    private $merchantRepository;
    private $userRepository;

    public function __construct(IMerchantRepository $iMerchantRepository,
    IUserRepository $iUserRepository) {
        $this->middleware('auth:api');
        $this->merchantRepository = $iMerchantRepository;
        $this->userRepository = $iUserRepository;
    }

    public function list(Request $request) {
        $this->authorize('viewAny', Merchant::class);
        $merchants = $this->merchantRepository->list($request->all(), true);
        return $this->responseWithData(200, $merchants);
    }

    public function listUsers(Request $request, Merchant $merchant) {
        $this->authorize('view', Merchant::class);
        $users = $this->userRepository->listMerchantUsers($merchant, $request->all(), true);
        return $this->responseWithData(200, $users);
    }

    public function listCategories(Request $request) {
        $merchantCategories = $this->merchantRepository->listCategories($request->all(), true);
        return $this->responseWithData(200, $merchantCategories);
    }

    public function track(TrackRequest $request) {
        $this->merchantRepository->track($request->all());
        return $this->responseWithMessage(200, 'Merchant page visit successfully tracked.');
    }

    public function details(Merchant $merchant) {
        $this->authorize('view', $merchant);
        return $this->responseWithData(200, $merchant);
    }

    public function create(CreateRequest $request) {
        $this->authorize('create', Merchant::class);
        $merchant = $this->merchantRepository->create($request->all(), $request->files->all());
        return $this->responseWithMessageAndData(201, $merchant, 'Merchant created.');
    }

    public function update(UpdateRequest $request, Merchant $merchant) {
        $this->authorize('update', $merchant);
        $merchant = $this->merchantRepository->update($merchant, $request->all(), $request->files->all());
        return $this->responseWithMessageAndData(200, $merchant, 'Merchant updated.');
    }

    // public function delete(Merchant $merchant) {
    //     // $this->authorize('delete', $merchant);
    //     $this->merchantRepository->delete($merchant);
    //     return $this->responseWithMessage(200, 'Merchant deleted.');
    // }

    public function createUsers(Merchant $merchant, CreateUsersRequest $request) {
        $this->authorize('update', $merchant);
        $users = $this->merchantRepository->createUsers($merchant, $request->all());
        foreach ($users as $user) {
            $token = Password::broker()->createToken($user);
            $user->sendMerchantWelcomeNotification($token);
        }

        return $this->responseWithMessage(200, 'Merchant user(s) created.');
    }

    public function deleteMerchantCategory(MerchantCategory $merchantCategory) {
        $this->merchantRepository->deleteMerchantCategory($merchantCategory);
        return $this->responseWithMessage(200, 'Category deleted.');
    }
}
