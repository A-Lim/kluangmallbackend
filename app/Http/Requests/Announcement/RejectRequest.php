<?php

namespace App\Http\Requests\Announcement;

use App\Announcement;
use App\Http\Requests\CustomFormRequest;

class RejectRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'remark' => 'required|string'
        ];
    }

    public function messages() {
        return [
        ];
    }
}