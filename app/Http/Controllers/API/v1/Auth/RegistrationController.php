<?php
namespace App\Http\Controllers\API\v1\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\RegistrationRequest;

use Carbon\Carbon;
use App\User;
use App\SystemSetting;
use App\Repositories\User\IUserRepository;
use App\Repositories\Auth\IOAuthRepository;
use App\Repositories\SystemSetting\ISystemSettingRepository;

use Illuminate\Http\Request;
class RegistrationController extends ApiController {

    private $userRepository;
    private $oAuthRepository;
    private $systemSettingRepository;

    public function __construct(IUserRepository $iUserRepository,
        IOAuthRepository $iOAuthRepository,
        ISystemSettingRepository $iSystemSettingRepository) {
        $this->middleware('guest');

        $this->userRepository = $iUserRepository;
        $this->oAuthRepository = $iOAuthRepository;
        $this->systemSettingRepository = $iSystemSettingRepository;
    }

    public function register(RegistrationRequest $request) {
        $registration_data = $request->all();
        $registration_message = 'Registration successful.';

        // check is registration is open
        $public_registration = $this->systemSettingRepository->findByCode('allow_public_registration');
        if (!(bool) $public_registration->value)
            return $this->responseWithMessage(403, 'Registration is closed.');

        // verification type
        $verification_type = $this->systemSettingRepository->findByCode('verification_type');
        // no verification
        if ($verification_type->value == SystemSetting::VTYPE_NONE)
            $registration_data['status'] = User::STATUS_ACTIVE;

        $user = $this->userRepository->create($registration_data);

        // send email verification email
        if ($verification_type->value == SystemSetting::VTYPE_EMAIL) {
            $registration_message .= ' An email will be sent to verify your account.';
            event(new Registered($user));
        }
        
        // assign default usergroup
        $default_usergroup = $this->systemSettingRepository->findByCode('default_usergroups');
        if (!empty($default_usergroup->value))
            $user->assignUserGroupsByIds($default_usergroup->value);

        return $this->responseWithMessage(200, $registration_message);
    }
}
