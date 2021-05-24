<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\CustomFormRequest;

use App\Voucher;

class ReportRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'merchant_id' => 'required|exists:merchants,id',
            'fromDate' => 'required|date:d M Y',
            'toDate' => 'required|date:d M Y',
        ];
    }
}
