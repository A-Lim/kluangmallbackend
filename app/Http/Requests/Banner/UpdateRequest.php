<?php

namespace App\Http\Requests\Banner;

use App\Banner;
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
            'title' => 'required|string',
            'status' => 'required|in:'.implode(',', Banner::STATUSES),
            'is_clickable' => 'required',
            'image' => 'nullable',
            'uploadImage' => 'nullable|image',
            'type' => 'required_if:is_clickable,true',
            'type_id' => 'required_if:is_clickable,true|integer'
        ];
    }

    public function messages() {
        return [
        ];
    }
}
