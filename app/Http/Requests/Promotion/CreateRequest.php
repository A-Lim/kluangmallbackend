<?php

namespace App\Http\Requests\Promotion;

use App\Promotion;
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
            'uploadThumbnail' => 'image|nullable',
            'fromDate' => 'required|date_format:d M Y',
            'toDate' => 'required|date_format:d M Y',
            'uploadImages.*' => 'image|nullable',
            'caption' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:'.implode(',', Promotion::STATUSES)
        ];
    }

    public function messages() {
        return [
            'uploadThumbnail.image' => 'Thumbnail must be an image.',
            'uploadImages.*.image' => 'Only image is allowed.',
        ];
    }
}