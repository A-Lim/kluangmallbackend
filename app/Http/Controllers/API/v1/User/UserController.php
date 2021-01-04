<?php

namespace App\Http\Controllers\API\v1\User;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Http\Resources\Users\UserResource;
use App\Repositories\User\IUserRepository;

use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UploadAvatarRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;

class UserController extends ApiController {

    private $userRepository;

    public function __construct(IUserRepository $iUserRepository) {
        $this->middleware('auth:api');
        $this->userRepository = $iUserRepository;
    }
    
    public function list(Request $request) {
        if ($request->type != 'formcontrol')
            $this->authorize('viewAny', User::class);
            
        $users = $this->userRepository->list($request->all(), true);
        return $this->responseWithData(200, $users);
    }

    public function profile() {
        return $this->responseWithData(200, auth()->user()); 
    }

    public function updateProfile(UpdateProfileRequest $request) {
        $authUser = auth()->user();
        $this->authorize('updateProfile', $authUser);

        // prevent user from updating anything else like status, verified_at etc
        $data = $request->only(['name', 'phone', 'gender', 'date_of_birth']);

        // if user has oldPassword filled,
        // user attempting to change password
        if ($request->has('oldPassword')) {
            $credentials = ['email' => $authUser->email, 'password' => $request->oldPassword];
            
            if (!Auth::guard('web')->attempt($credentials)) {
                return $this->responseWithMessage(401, 'Invalid old password.');
            }
            $data['password'] = $request->newPassword;
        }

        $user = $this->userRepository->update(auth()->user(), $data);
        $userResource = new UserResource($user);
        return $this->responseWithMessageAndData(200, $userResource, 'Profile updated.');  
    }

    public function changePassword(ChangePasswordRequest $request) {
        $user = auth()->user();
        $credentials = ['email' => $user->email, 'password' => $request->oldPassword];
        if (!Auth::guard('web')->attempt($credentials)) {
            return $this->responseWithMessage(401, 'Invalid old password.');
        }

        $user = $this->userRepository->updatePassword(auth()->user(), $request->newPassword);
        $userResource = new UserResource($user);
        return $this->responseWithMessageAndData(200, $userResource, 'Password updated.'); 
    }

    public function uploadProfileAvatar(UploadAvatarRequest $request) {
        $user = auth()->user();
        $this->authorize('updateProfile', $user);
        // $imagePaths = $this->userRepository->saveAvatar(auth()->user(), $request->file('avatar'));
        $imagePath = $this->userRepository->saveAvatarBasic($user, $request->file('avatar'));
        return $this->responseWithMessageAndData(200, $imagePath, 'Profile avatar updated.');
    }

    public function uploadUserAvatar(UploadAvatarRequest $request, User $user) {
        $this->authorize('update', $user);
        // $imagePaths = $this->userRepository->saveAvatar($user, $request->file('avatar'));
        $imagePath = $this->userRepository->saveAvatarBasic($user, $request->file('avatar'));
        return $this->responseWithMessageAndData(200, $imagePath, 'User avatar updated.');
    }
 
    public function details(User $user) {
        $this->authorize('view', $user);
        $user = $this->userRepository->findWithUserGroups($user->id);
        $userResource = new UserResource($user);
        return $this->responseWithData(200, $userResource); 
    }

    public function update(UpdateRequest $request, User $user) {
        $this->authorize('update', $user);
        $data = $request->only(['name', 'phone', 'date_of_birth', 'gender', 'status', 'usergroups']);
        $user = $this->userRepository->update($user, $data);
        $userResource = new UserResource($user);
        return $this->responseWithMessageAndData(200, $userResource, 'User updated.'); 
    }

    public function resetPassword(Request $request, User $user) {
        $this->authorize('update', $user);
        $random_password = $this->userRepository->randomizePassword($user);
        return $this->responseWithData(200, $random_password);
    }

}
