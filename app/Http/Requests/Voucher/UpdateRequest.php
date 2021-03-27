<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\CustomFormRequest;

use App\Voucher;
use App\VoucherLimit;

class UpdateRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    protected function prepareForValidation()   {
        $this->merge([
            'has_redemption_limit' => filter_var($this->has_redemption_limit, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules() {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'points' => 'required|integer',
            'fromDate' => 'required|date_format:d M Y',
            'toDate' => 'required|date_format:d M Y|after_or_equal:fromDate',
            'has_redemption_limit' => 'required|boolean',
            'limits' => 'required_if:has_redemption_limit,true',
            'terms_and_conditions' => 'required|string',
            'limits.*.type' => 'nullable|distinct|in:'.implode(',', VoucherLimit::TYPES),
            'limits.*.value' => 'required_with:limits.*.type|integer',
        ];
    }
}
