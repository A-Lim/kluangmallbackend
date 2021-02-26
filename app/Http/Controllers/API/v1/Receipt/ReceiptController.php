<?php

namespace App\Http\Controllers\API\v1\Receipt;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use App\Receipt;
use App\Merchant;
use App\PointTransaction;
use App\Http\Requests\Receipt\UploadRequest;
use App\Repositories\Receipt\IReceiptRepository;
use App\Repositories\Merchant\IMerchantRepository;
use App\Repositories\PointTransaction\IPointTransactionRepository;

class ReceiptController extends ApiController {

    private $merchantRepository;
    private $receiptRepository;
    private $pointTransactionRepository;

    public function __construct(IReceiptRepository $iReceiptRepository,
        IPointTransactionRepository $iPointTransactionRepository,
        IMerchantRepository $iMerchantRepository) {
        $this->middleware('auth:api');
        $this->receiptRepository = $iReceiptRepository;
        $this->merchantRepository = $iMerchantRepository;
        $this->pointTransactionRepository = $iPointTransactionRepository;
    }

    public function list(Request $request) {
        $receipts = $this->receiptRepository->list($request->all(), true);
        return $this->responseWithData(200, $receipts);
    }

    public function listMy(Request $request) {
        $user = auth()->user();
        $receipts = $this->receiptRepository->listMy($user, $request->all(), true);
        return $this->responseWithData(200, $receipts);
    }

    public function upload(UploadRequest $request) {
        $user = auth()->user();
        $merchant = $this->merchantRepository->find($request->merchant_id);

        if ($merchant == null)
            return $this->responseWithMessage(400, 'Invalid merchant.');

        // format date
        $date = Carbon::createFromFormat(env('DATE_FORMAT'), $request->date);
        // upload receipt to s3
        $saveDirectory = 'receipts/'.$date->format('d-m-Y');
        $path = Storage::disk('s3')->putFile($saveDirectory, $request->image, 'public');
        $url = Storage::disk('s3')->url($path);

        // upload to other service
        $response = $this->checkValidity($merchant, $path, $request->amount, $date->format('d-m-Y'));

        if (!$response['is_valid']) {
            Storage::disk('s3')->delete($path);
            return $this->responseWithMessage(400, $response['message']);
        }

        if ($this->receiptRepository->exists($response['invoice_number']))
            return $this->responseWithMessage(400, 'Receipt has already been used.');

        $receiptData = [
            'invoice_no' => $response['invoice_number'],
            'merchant_id' => $request->merchant_id,
            'user_id' => $user->id,
            'image' => $url,
            'amount' => $request->amount,
            'date' => $date
        ];

        $receipt = $this->receiptRepository->create($receiptData);

        $data = [
            // 'type' => PointTransaction::TYPE_PENDING,
            'type' => PointTransaction::TYPE_ADD,
            'amount' => $receipt->points,
            'description' => 'Earned '.$receipt->points.' points from '.$receipt->merchant->name.'.'
        ];

        $this->pointTransactionRepository->create($user, $data, $receipt);

        return $this->responseWithMessage(200, 'Receipt upload successful.');
    }

    private function checkValidity(Merchant $merchant, $path, $amount, $date) {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post(env('RECEIPTSERVICEURL'), [
            'path' => $path,
            'keywords' => json_encode([$merchant->name, 'Kluang Mall']),
            'amount' => $amount, 
            'date' => $date
        ]);
    }
}
