<?php

namespace App\Http\Requests\User;

use App\Http\Requests\CustomFormRequest;

class ChangePasswordRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|confirmed',
        ];
    }

    public function messages() {
        return [
            'newPassword.required_with' => 'New password is required.',
        ];
    }
}
