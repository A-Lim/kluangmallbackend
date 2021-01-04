<?php
namespace App\Repositories\Merchant;

use App\Merchant;
use App\MerchantAccount;
use App\MerchantAccountTransaction;

class MerchantAccountRepository implements IMerchantAccountRepository {

    /**
     * {@inheritdoc}
     */
    public function topUp(Merchant $merchant, $data) {
        if (!$merchant->account()->exists())
            $merchant->account()->create(['credit' => 0]);

        $merchant_account = $merchant->account;

        // add credit 
        $merchant_account
            ->update(['credit' => $merchant_account->credit + $data['credit']]);

        $transaction = $merchant->transactions()->create([
            'title' => 'Credit Top Up',
            'credit' => $data['credit'],
            'type' => MerchantAccountTransaction::TYPE_TOPUP,
            'remark' => @$data['remark'],
            'created_by' => auth()->id()
        ]);

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function refund(Merchant $merchant, MerchantAccountTransaction $transaction, $data) {
        $merchant_account = $merchant->account;

        // deduct credit
        $merchant_account
            ->update(['credit' => $merchant_account->credit - $transaction->credit]);

        $refund_transaction = $merchant->transactions()->create([
            'title' => 'Credit Refund',
            'credit' => $transaction->credit,
            'type' => MerchantAccountTransaction::TYPE_REFUND,
            'refund_transaction_id' => $transaction->id,
            'remark' => @$data['remark'],
            'created_by' => auth()->id()
        ]);

        $transaction->refunded = true;
        $transaction->save();

        return $refund_transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function deduct(Merchant $merchant, $data) {
        if (!$merchant->account()->exists())
            $merchant->account()->create(['credit' => 0]);

        $merchant_account = $merchant->account;

        $merchant_account
            ->update(['credit' => $merchant_account->credit - $data['credit']]);

        $transaction = $merchant->transactions()->create([
            'title' => $data['title'],
            'credit' => $data['credit'],
            'type' => MerchantAccountTransaction::TYPE_DEDUCT,
            'remark' => @$data['remark'],
            'created_by' => auth()->id()
        ]);

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function reCredit(Merchant $merchant, $data) {
        if (!$merchant->account()->exists())
            $merchant->account()->create(['credit' => 0]);

        $merchant_account = $merchant->account;

        $merchant_account
            ->update(['credit' => $merchant_account->credit + $data['credit']]);

        $transaction = $merchant->transactions()->create([
            'title' => $data['title'],
            'credit' => $data['credit'],
            'type' => MerchantAccountTransaction::TYPE_RECREDIT,
            'remark' => @$data['remark'],
            'created_by' => auth()->id()
        ]);

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function listTransactions(Merchant $merchant, $data, $paginate = false) {
        $query = null;

        if ($data)
            $query = MerchantAccountTransaction::buildQuery($data);
        else 
            $query = MerchantAccountTransaction::query()->orderBy('id', 'desc');
        
        $query = $query->where('merchant_id', $merchant->id);

        $query->orderBy('id', 'desc');
        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Merchant::with('account')->buildQuery($data);
        else 
            $query = Merchant::query()->orderBy('id', 'desc');

        $query->orderBy('id', 'desc');
        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }
}