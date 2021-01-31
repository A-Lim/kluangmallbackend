<?php

namespace App\Http\Controllers\API\v1\Receipt;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Receipt;
use App\Http\Requests\Receipt\UploadRequest;
use App\Repositories\Receipt\IReceiptRepository;

class ReceiptController extends ApiController {

    private $receiptRepository;

    public function __construct(IReceiptRepository $iReceiptRepository) {
        $this->middleware('auth:api');
        $this->receiptRepository = $iReceiptRepository;
    }

    public function upload(UploadRequest $request) {
        $user = auth()->user();

        // upload to other service

        $this->receiptRepository->upload($user, $request->all(), $request->file('image'));

        return $this->responseWithMessage(200, 'Receipt upload successful.');
    }
}
