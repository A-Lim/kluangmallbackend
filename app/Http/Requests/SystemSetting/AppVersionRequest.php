<?php

namespace App\Http\Requests\SystemSetting;

use App\SystemSetting;
use App\Http\Requests\CustomFormRequest;

class AppVersionRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'type' => 'required|in:user,merchant',
            'os' => 'required|in:android,ios',
        ];
    }
}
