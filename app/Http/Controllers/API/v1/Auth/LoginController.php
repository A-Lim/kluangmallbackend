<?php
namespace App\Http\Controllers\API\v1\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;

use App\Http\Controllers\ApiController;

use Carbon\Carbon;
use App\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\FbLoginRequest;

use App\Repositories\Auth\IOAuthRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\SystemSetting\ISystemSettingRepository;

class LoginController extends ApiController {

    use AuthenticatesUsers, ThrottlesLogins;

    private $oAuthRepository;
    private $userRepository;
    private $systemSettingRepository;

    public function __construct(IOAuthRepository $iOAuthRepository,
        IUserRepository $iUserRepository,
        ISystemSettingRepository $iSystemSettingRepository) {
        $this->middleware('guest')->only(['login', 'fbLogin']);
        $this->middleware('auth:api')->only('logout');

        $this->oAuthRepository = $iOAuthRepository;
        $this->userRepository = $iUserRepository;
        $this->systemSettingRepository = $iSystemSettingRepository;
    }

    public function login(LoginRequest $request) {
        // if too many login attemps
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        
        if ($this->attemptLogin($request)) {
            $this->clearLoginAttempts($request);
            
            $user = auth()->user();
            return $this->logUserIn($user, $request->device_token);
        }

        // if unsuccessful, increase login attempt count
        // lock user count limit reached
        $this->incrementLoginAttempts($request);

        return $this->responseWithMessage(401, 'Invalid login credentials.');
    }

    public function userLogin(LoginRequest $request) {
        // if too many login attemps
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        
        if ($this->attemptLogin($request)) {
            $this->clearLoginAttempts($request);
            
            $user = auth()->user();
            if ($user->merchant != null)
                return $this->responseWithMessage(401, 'Invalid user account.');

            return $this->logUserIn($user, $request->device_token);
        }

        // if unsuccessful, increase login attempt count
        // lock user count limit reached
        $this->incrementLoginAttempts($request);

        return $this->responseWithMessage(401, 'Invalid login credentials.');
    }

    public function merchantLogin(LoginRequest $request) {
        // if too many login attemps
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        
        if ($this->attemptLogin($request)) {
            $this->clearLoginAttempts($request);
            
            $user = auth()->user();
            if ($user->merchant == null)
                return $this->responseWithMessage(401, 'Invalid merchant account.'); 
            
            return $this->logUserIn($user, $request->device_token);
        }

        // if unsuccessful, increase login attempt count
        // lock user count limit reached
        $this->incrementLoginAttempts($request);

        return $this->responseWithMessage(401, 'Invalid login credentials.');
    }

    public function fbLogin(FbLoginRequest $request) {
        // check token valid
        $result = $this->validateFbAccessToken($request->fb_access_token);

        if (@$result->error)
            return $this->responseWithMessage(401, 'Invalid token.');

        if (!@$result->email)
            return $this->responseWithMessage(401, 'Unable to retrieve facebook email.');
        
        if (@$result->email) {
            // check if account exists
            $user = $this->userRepository->searchForOne(['email' => $result->email]);

            if ($user) {
                if ($user->merchant != null)
                    return $this->responseWithMessage(401, 'Invalid user account.');
                else 
                    return $this->logUserIn($user, $request->device_token);
            }
            
            // create new user from fb details
            $new_user = [
                'name' => $result->name,
                'email' => $result->email,
                'password' => Str::random(10),
                'status' => User::STATUS_ACTIVE,
                'date_of_birth' => $result->birthday ? Carbon::createFromFormat('d/m/Y', $result->birthday) : null,
                'gender' => $result->gender
            ];

            $user = $this->userRepository->create($new_user);
            
            if (isset($request->fb_avatar))
                $this->userRepository->saveAvatarBasic($user, $request->fb_avatar);

            // assign default usergroup
            $default_usergroup = $this->systemSettingRepository->findByCode('default_usergroups');
            if (!empty($default_usergroup->value))
                $user->assignUserGroupsByIds($default_usergroup->value);

            return $this->logUserIn($user, $request->device_token);
        }
        
        return $this->responseWithMessage(401, 'Facebook login failed. Please contact administrator.');
    }

    public function logout(Request $request) {
        $accessToken = auth()->user()->token();
        // revoke refresh token
        $this->oAuthRepository->revokeRefreshToken($accessToken->id);
        // revoke access token
        $accessToken->revoke();

        return $this->responseWithMessage(200, "Successfully logged out.");
    }

    private function logUserIn(User $user, $device_token) {
        switch ($user->status) {
            case User::STATUS_LOCKED:
                return $this->responseWithMessage(401, 'This account is locked. Please contact administrator.');
                break;
            
            case User::STATUS_UNVERIFIED:
                return $this->responseWithMessage(401, 'This account is unverified. Please verify your email');
                break;
        }

        $tokenResult = $user->createToken('accesstoken');

        // update device token
        if ($device_token)
            $this->userRepository->updateDeviceToken($user, $device_token);

        if ($user->status == User::STATUS_INACTIVE)
            $permissions = $this->systemSettingRepository->findByCode('inactive_permissions')->value;
        else
            $permissions = $this->userRepository->permissions($user);
        
        return $this->responseWithLoginData(200, $tokenResult, $user, $permissions);
    }

    private function validateFbAccessToken($token) {
        $url = "https://graph.facebook.com/me?fields=name,email&access_token=".$token;
        $headers = ['Content-Type: application/json'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return json_decode($result);
    }
}
