<?php
namespace App\Repositories\Receipt;

use App\User;

interface IReceiptRepository {

    /**
     * Check is receipt already exist
     * @param string $invoice_no
     * @return boolean
     */
    public function exists($invoice_no);

    /**
     * List User's receipts
     * @param User $user
     * @param array $data
     * @param boolean $paginate
     * @return [Receipt]
     */
    public function listMy(User $user, $data, $paginate = false);

    /**
     * Upload receipt
     * @return Receipt
     */
    // public function upload(User $user, $data);
}