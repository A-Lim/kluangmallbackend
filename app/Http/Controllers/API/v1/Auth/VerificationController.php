<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

use App\Http\Requests\Auth\VerifyOTPRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\SendVerificationEmailRequest;
use App\Repositories\User\IUserRepository;

class VerificationController extends ApiController {

    private $userRepository;

    public function __construct(IUserRepository $iUserRepository) {
        $this->middleware('signed')->only('verifyEmail');
        $this->middleware('throttle:6,1')->only('verifyEmail', 'resend');

        $this->userRepository = $iUserRepository;
    }

    public function verifyEmail(VerifyEmailRequest $request) {
        $statusCode = '';
        $message = '';

        $user = $this->userRepository->searchForOne(['email' => $request->email]);
        if ($user == null) {
            $statusCode = 401;
            $message = 'User not found.';
        }

        if (!hash_equals((string) $request->get('id'), (string) $user->getKey())) {
            $statusCode = 401;
            $message = 'Invalid id.';
        }

        if (!hash_equals((string) $request->get('hash'), sha1($user->getEmailForVerification()))) {
            $statusCode = 401;
            $message = 'Invalid hash.';
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            $statusCode = 200;
            $message = 'Email succesfully verified.';
        }

        return $this->responseWithMessage($statusCode, $message);
    }

    public function verifyOTP(VerifyOTPRequest $request) {
        $user = $this->userRepository->searchForOne(['email' => $request->email]);

        if (!$user->isOtpValid($request->otp))
            return $this->responseWithMessage(400, 'Invalid OTP.');
           
        $otp_token = $this->userRepository->generateOtpToken($user);
        return $this->responseWithData(200, $otp_token);
    }

    public function sendVerificationEmail(SendVerificationEmailRequest $request) {

        $user = $this->userRepository->searchForOne(['email' => $request->email]);
        $user->sendEmailVerificationNotification();
        
        if ($user->hasVerifiedEmail()) {
            return $this->responseWithMessage(400, 'Email is already verified.');
        }

        return $this->responseWithMessage(200, 'Verification email sent.');
    }
}
