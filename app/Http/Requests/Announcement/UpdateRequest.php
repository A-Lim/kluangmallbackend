<?php

namespace App\Http\Requests\Announcement;

use App\Announcement;
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
            'description' => 'required|string',
            'has_content' => 'required',
            'content' => 'required_if:has_content,true',
            'uploadImage' => 'image|nullable',
            'audience' => 'nullable|in:'.implode(',', Announcement::AUDIENCES),
            'status' => 'nullable|in:'.implode(',', Announcement::STATUSES),
        ];
    }

    public function messages() {
        return [
        ];
    }
}