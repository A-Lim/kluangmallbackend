<?php

namespace App\Http\Controllers\API\v1\Merchant;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

use App\Merchant;
use App\MerchantAccountTransaction;
use App\Repositories\Merchant\IMerchantAccountRepository;
use App\Notifications\Merchant\MerchantAccountToppedUp;
use App\Notifications\Merchant\MerchantCreditRefunded;

use App\Http\Requests\Merchant\TopUpRequest;

class MerchantAccountController extends ApiController {

    private $merchantAccountRepository;

    public function __construct(IMerchantAccountRepository $iMerchantAccountRepository) {
        $this->middleware('auth:api');
        $this->merchantAccountRepository = $iMerchantAccountRepository;
    }

    public function listTransactions(Merchant $merchant, Request $request) {
        $transactions = $this->merchantAccountRepository->listTransactions($merchant, $request->all(), true);
        return $this->responseWithData(200, $transactions);
    }

    public function listMyTransactions(Request $request) {
        $user = auth()->user();
        $merchant = $user->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        $transactions = $this->merchantAccountRepository->listTransactions($merchant, $request->all(), true);
        return $this->responseWithData(200, $transactions);
    }

    public function topup(TopUpRequest $request, Merchant $merchant) {
        // $this->authorize('', Merchant::class);
        $transaction = $this->merchantAccountRepository->topUp($merchant, $request->all());
        $credit = $merchant->account->credit;

        $data = [
            'credit_balance' => $credit,
            'transaction' => $transaction
        ];

        $merchant->notify(new MerchantAccountToppedUp($transaction));
        return $this->responseWithMessageAndData(200, $data, 'Merchant credit topped up.');
    }

    public function topUpMyAccount(TopUpRequest $request) {
        $user = auth()->user();
        $merchant = $user->merchant;

        if (!$merchant)
            return $this->responseWithMessage(400, 'Invalid merchant account.');

        $transaction = $this->merchantAccountRepository->topUp($merchant, $request->all());
        $credit = $merchant->account->credit;

        $data = [
            'credit_balance' => $credit,
            'transaction' => $transaction
        ];

        $merchant->notify(new MerchantAccountToppedUp($transaction));
        return $this->responseWithMessageAndData(200, $data, 'Merchant credit topped up.');
    }

    public function refund(Request $request, MerchantAccountTransaction $transaction) {
        $merchant = $transaction->merchant;
        if ($transaction->type != MerchantAccountTransaction::TYPE_TOPUP || 
            $transaction->type != MerchantAccountTransaction::TYPE_DEDUCT)
            return $this->responseWithMessage(403, 'Unable to refund this transaction.');

        if ($transaction->refunded)
            return $this->responseWithMessage(403, 'Unable to refund a refunded transaction.');

        if ($merchant->account->credit - $transaction->credit < 0)
            return $this->responseWithMessage(403, 'Insufficient merchant credit to be deducted.');

        $transaction = $this->merchantAccountRepository->refund($merchant, $transaction, $request->all());
        $credit = $merchant->account->credit;

        $data = [
            'credit_balance' => $credit,
            'transaction' => $transaction
        ];

        $merchant->notify(new MerchantCreditRefunded($transaction));
        return $this->responseWithMessageAndData(200, $data, 'Merchant credit refunded.');
    }

    // public function undo() {

    // }

    // public function deduct() {

    // }
}
