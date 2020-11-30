<?php

namespace App\Http\Requests\Merchant;

use App\Merchant;
use App\Http\Requests\CustomFormRequest;

class CreateRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name' => 'required|string',
            'uploadLogo' => 'required|image|nullable',
            'status' => 'required|in:'.implode(',', Merchant::STATUSES),
            'location' => 'required|string',
            'category' => 'required|string',
            'business_reg_no' => 'required|string',
            'website' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'description' => 'required|string',
            'terms_and_conditions' => 'nullable',
            'privacy_policy' => 'nullable'
        ];
    }

    public function messages() {
        return [
            'uploadLogo.required' => 'Logo is required.',
            'uploadLogo.image' => 'Logo must be an image.',
        ];
    }
}