<?php

namespace App\Http\Requests\Announcement;

use App\Announcement;
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
            'publish_now' => filter_var($this->publish_now, FILTER_VALIDATE_BOOLEAN),
            'has_content' => filter_var($this->has_content, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules() {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'publish_now' => 'required|boolean',
            'publish_at' => 'required_if:publish_now,false|date_format:d M Y|after:now',
            'has_content' => 'required|boolean',
            'content' => 'required_if:has_content,true',
            'uploadImage' => 'image|nullable',
            'audience' => 'nullable|in:'.implode(',', Announcement::AUDIENCES),
            'status' => 'nullable|in:'.implode(',', Announcement::STATUSES),
        ];
    }

    public function messages() {
        return [
            'publish_at.after' => 'The :attribute must be a date greater than today.'
        ];
    }
}