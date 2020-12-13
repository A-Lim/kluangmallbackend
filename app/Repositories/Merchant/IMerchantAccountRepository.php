<?php
namespace App\Repositories\Merchant;

use App\Merchant;
use App\MerchantAccountTransaction;

interface IMerchantAccountRepository {

    /**
     * Top Up credit to merchant account
     * @param Merchant $merchant 
     * @param array $data
     * @return array MerchantAccountTransaction
     */
    public function topUp(Merchant $merchant, $data);

    /**
     * Refund an existing transaction
     * @param Merchant $merchant 
     * @param MerchantAccountTransaction $transaction
     * @param array $data
     * @return array MerchantAccountTransaction
     */
    public function refund(Merchant $merchant, MerchantAccountTransaction $transaction, $data);

    /**
     * List all transactions for a merchant
     * @param Merchant $merchant 
     * @param array $data
     * @return array [MerchantAccountTransaction]
     */
    public function listTransactions(Merchant $merchant, $data, $paginate = false);
}