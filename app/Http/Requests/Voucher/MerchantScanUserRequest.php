<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\CustomFormRequest;

class MerchantScanUserRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'user_id' => 'required|integer'
        ];
    }
}
