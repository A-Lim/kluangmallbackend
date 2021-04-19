<?php

namespace App\Http\Controllers\API\v1\FileUpload;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FileUpload\FileUploadRequest;

class FileUploadController extends ApiController {

    public function __construct() {
        $this->middleware('auth:api');
    }

    public function upload(FileUploadRequest $request) {
        $today = Carbon::today();
        $saveDirectory = 'fileuploads/'.$today->format(env('DATE_FORMAT'));

        $path = Storage::disk('s3')->putFile($saveDirectory, $request->file, 'public');
        return $this->responseWithData(200, Storage::disk('s3')->url($path));
    }
}
