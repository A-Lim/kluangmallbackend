<?php
namespace App\Http\Controllers\API\v1\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Passport\Passport;

use App\Http\Controllers\ApiController;

use Carbon\Carbon;
use App\User;
use App\Http\Requests\Auth\LoginRequest;

use App\Repositories\Auth\IOAuthRepository;
use App\Repositories\User\IUserRepository;
use App\Repositories\SystemSetting\ISystemSettingRepository;

class LoginController extends ApiController {

    use AuthenticatesUsers, ThrottlesLogins;

    private $oAuthRepository;
    private $userRepository;
    private $systemRepository;

    public function __construct(IOAuthRepository $iOAuthRepository,
        IUserRepository $iUserRepository,
        ISystemSettingRepository $iSystemSettingRepository) {
        $this->middleware('guest')->only(['login', 'fbLogin']);
        $this->middleware('auth:api')->only('logout');

        $this->oAuthRepository = $iOAuthRepository;
        $this->userRepository = $iUserRepository;
        $this->systemRepository = $iSystemSettingRepository;
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
            if ($request->filled('device_token'))
                $this->userRepository->updateDeviceToken($user, $request->device_token);

            if ($user->status == User::STATUS_INACTIVE)
                $permissions = $this->systemRepository->findByCode('inactive_permissions')->value;
            else
                $permissions = $this->userRepository->permissions($user);
            
            // dd($user->toArray());
            return $this->responseWithLoginData(200, $tokenResult, $user, $permissions);
        }

        // if unsuccessful, increase login attempt count
        // lock user count limit reached
        $this->incrementLoginAttempts($request);

        return $this->responseWithMessage(401, 'Invalid login credentials.');
    }

    public function fbLogin(Request $request) {
        // check token valid
        $result = $this->validateFbAccessToken($request->token);

        if ($result->error)
            return $this->responseWithMessage(401, 'Invalid token.');

        if ($result->data->app_id != env('FACEBOOK_APP_ID'))
            return $this->responseWithMessage(401, 'Invalid app id.');

        if ($result->data->is_valid)
            return $this->responseWithMessage(401, 'Invalid token.');

        // check if account exists
        // if ()

        // yes - login

        // no - register with status active, populated with facebook profile picture, random generated password
    }

    public function logout(Request $request) {
        $accessToken = auth()->user()->token();
        // revoke refresh token
        $this->oAuthRepository->revokeRefreshToken($accessToken->id);
        // revoke access token
        $accessToken->revoke();

        return $this->responseWithMessage(200, "Successfully logged out.");
    }

    private function validateFbAccessToken($token) {
        $url = "https://graph.facebook.com/v10.0/debug_token?input_token=".$token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $result;
    }
}
