<?php

namespace App\Http\Requests\Promotion;

use App\Promotion;
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
            'uploadThumbnail' => 'image|nullable',
            'fromDate' => 'required|date_format:d M Y',
            'toDate' => 'required|date_format:d M Y',
            'images' => 'nullable',
            'images.*.name' => 'required',
            'images.*.path' => 'required',
            'uploadImages.*' => 'image|nullable',
            'caption' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:'.implode(',', Promotion::STATUSES)
        ];
    }

    public function messages() {
        return [
            'uploadImages.*.image' => 'Only image is allowed.',
        ];
    }
}
