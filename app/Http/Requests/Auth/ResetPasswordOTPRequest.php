<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\CustomFormRequest;

class ResetPasswordOTPRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'otp_token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }
}
