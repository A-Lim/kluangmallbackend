<?php
namespace App\Repositories\Voucher;

use App\User;
use App\MyVoucher;

interface IMyVoucherRepository {

    /**
     * List all users' active vouchers
     * @param User $user
     * @param boolean $paginate
     * @return [MyVoucher] 
     */
    public function listActive(User $user, $paginate = false);

    /**
     * List all users' inactive vouchers
     * @param User $user
     * @param boolean $paginate
     * @return [MyVoucher] 
     */
    public function listInactive(User $user, $paginate = false);

    /**
     * Find MyVoucher
     * @param integer $id
     * @return MyVoucher
     */
    public function find($id);

    /**
     * List users' voucher details
     * @param integer $id
     * @return MyVoucher 
     */
    public function details($id);

    /**
     * Consume user's voucher
     * @param User $user
     * @param MyVoucher $myVoucher
     * @return MyVoucher
     */
    public function use(User $user, MyVoucher $myVoucher);

    /**
     * Update expired users' voucher
     * @return void
     */
    public function updateExpired();
}