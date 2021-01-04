<?php

namespace App\Http\Requests\Merchant;

use App\Http\Requests\CustomFormRequest;

class TopUpRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'credit' => 'required|numeric',
            'remark' => 'nullable|string',
        ];
    }

    public function messages() {
        return [
        ];
    }
}