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

    protected function prepareForValidation()   {
        $this->merge([
            'is_clickable' => filter_var($this->is_clickable, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules() {
        return [
            'title' => 'required|string',
            'status' => 'required|in:'.implode(',', Banner::STATUSES),
            'app' => 'required|in:'.implode(',', Banner::APPS),
            'is_clickable' => 'required|boolean',
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
