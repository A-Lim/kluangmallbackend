<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\CustomFormRequest;

class UserScanMerchantRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'data' => 'required'
        ];
    }
}
