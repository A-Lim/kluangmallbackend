<?php

namespace App\Http\Requests\Merchant;

use App\Http\Requests\CustomFormRequest;

class TrackRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'merchant_id' => 'required|string',
            'device_id' => 'required|string',
        ];
    }

    public function messages() {
        return [
        ];
    }
}