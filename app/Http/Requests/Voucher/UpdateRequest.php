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
            'display_to_all' => filter_var($this->display_to_all, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules() {
        return [
            'type' => 'required|in:'.implode(',', Voucher::TYPES),
            'display_to_all' => 'nullable|boolean',
            'name' => 'required|string',
            'description' => 'required|string',
            'points' => 'required|integer',
            'free_points' => 'nullable|required_if:type,'.Voucher::TYPE_ADD_POINT.'|integer',
            'fromDate' => 'required|date_format:d M Y',
            'toDate' => 'required|date_format:d M Y|after_or_equal:fromDate',
            'uploadImage' => 'nullable|mimes:jpg,jpeg,png|max:5120',
            'has_redemption_limit' => 'required|boolean',
            'limits' => 'required_if:has_redemption_limit,true',
            'terms_and_conditions' => 'required|string',
            'limits.*.type' => 'nullable|distinct|in:'.implode(',', VoucherLimit::TYPES),
            'limits.*.value' => 'required_with:limits.*.type|integer',
        ];
    }
}
