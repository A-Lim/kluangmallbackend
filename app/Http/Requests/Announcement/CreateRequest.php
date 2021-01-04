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

    public function rules() {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'publish_now' => 'required',
            'publish_at' => 'required_if:publish_now,false|date_format:d M Y|after:now',
            'has_content' => 'required',
            'content' => 'required_if:has_content,true',
            'uploadImage' => 'image|nullable',
            'audience' => 'nullable|in:'.implode(',', Announcement::AUDIENCES),
            'status' => 'nullable|in:'.implode(',', Announcement::STATUSES),
            'now' => 'nullable'
        ];
    }

    public function messages() {
        return [
            'publish_at.after' => 'The :attribute must be a date greater than today.'
        ];
    }
}