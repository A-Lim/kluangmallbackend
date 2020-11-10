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
            'category' => 'nullable',
            'uploadThumbnail' => 'image|nullable',
            'fromDate' => 'required|date_format:d-m-Y',
            'toDate' => 'required|date_format:d-m-Y',
            'uploadImages.*' => 'image|nullable',
            'content' => 'required|string',
            'status' => 'required|in:'.implode(',', Promotion::STATUSES)
        ];
    }

    public function messages() {
        return [
            'uploadThumbnail' => 'Thumbnail must be an image.',
            'uploadImages.*.image' => 'Only image is allowed.',
        ];
    }
}