<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\CustomFormRequest;

class VerifyOTPRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|integer'
        ];
    }


}
