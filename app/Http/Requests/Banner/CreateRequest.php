<?php

namespace App\Http\Requests\Banner;

use App\Banner;
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
            'uploadBanners.*' => 'required|image'
        ];
    }

    public function messages() {
        return [
            'uploadBanners.*.required' => 'Banner is required.',
            'uploadBanners.*.image' => 'Only image is allowed.',
        ];
    }
}
