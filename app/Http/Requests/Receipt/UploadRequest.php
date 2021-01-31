<?php

namespace App\Http\Requests\Receipt;

use App\Http\Requests\CustomFormRequest;

class UploadRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'image' => 'required|image',
            'merchant_id' => 'required',
            'amount' => 'required',
            'date' => 'required|date_format:d M Y'
        ];
    }

    public function messages() {
        return [
        ];
    }
}