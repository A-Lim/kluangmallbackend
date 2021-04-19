<?php

namespace App\Http\Requests\FileUpload;

use App\Http\Requests\CustomFormRequest;

class FileUploadRequest extends CustomFormRequest {

    public function __construct() {
        parent::__construct();
    }
    
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'file' => 'required|file'
        ];
    }
}