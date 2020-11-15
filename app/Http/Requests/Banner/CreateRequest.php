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
            'title' => 'required|string',
            'status' => 'required|in:'.implode(',', Banner::STATUSES),
            'is_clickable' => 'required|boolean',
            'image' => 'required|image',
            'type' => 'required_if:is_clickable,true',
            'type_if' => 'required_if:is_clickable,true|integer'
        ];
    }

    public function messages() {
        return [
        ];
    }
}
