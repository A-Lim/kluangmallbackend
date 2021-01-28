<?php
namespace App\Repositories\Voucher;

use App\User;
use App\Voucher;
use App\VoucherLimit;
use App\Merchant;

interface IVoucherTransactionRepository {
    /**
     * List all vouchers transactions
     * @return array [VoucherTransaction]
     */
    public function list($data, $paginate = false);

    /**
     * Count number of redeemed vouchers by merchant
     * @param Merchant
     * @return integer
     */
    public function redeemCount(Merchant $merchant);
}