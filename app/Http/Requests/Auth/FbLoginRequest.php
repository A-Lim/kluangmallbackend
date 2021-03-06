<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\CustomFormRequest;

class FbLoginRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
        // set message
        $this->setMessage('Invalid login credentials.');
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'fb_id' => 'required|string',
            'fb_access_token' => 'required|string',
            'fb_avatar' => 'nullable|image',
            'device_token' => 'required|string'
        ];
    }
}
