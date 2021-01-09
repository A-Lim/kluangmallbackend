<?php

namespace App\Http\Requests\Landing;

use App\Landing;
use App\Http\Requests\CustomFormRequest;

class UpdateRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'app' => 'required|in:'.implode(',', Landing::APPS),
            'banners' => 'nullable',
            'events' => 'nullable',
            'promotions' => 'nullable'
        ];
    }

    public function messages() {
        return [
        ];
    }
}
