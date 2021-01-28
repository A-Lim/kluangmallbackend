<?php

namespace App\Http\Requests\Event;

use App\Http\Requests\CustomFormRequest;

class NewUserRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'range' => 'nullable'
        ];
    }

    public function messages() {
        return [
            'uploadThumbnail' => 'Thumbnail must be an image.',
            'uploadImages.*.image' => 'Only image is allowed.',
        ];
    }
}