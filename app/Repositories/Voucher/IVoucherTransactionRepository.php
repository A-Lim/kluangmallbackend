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
     * List all vouchers users' voucher transactions
     * @param User $user
     * @param boolean $paginate
     * @return array [VoucherTransaction]
     */
    public function listMy(User $user, $paginate = false);

    /**
     * Count number of redeemed vouchers by merchant
     * @param Merchant $merchant
     * @return integer
     */
    public function redeemCount(Merchant $merchant);

    /**
     * Redemption History of voucher
     * @param Voucher $voucher
     * @param boolean $paginate
     * @return integer
     */
    public function listRedemptionHistory(Voucher $voucher, $paginate = false);
}