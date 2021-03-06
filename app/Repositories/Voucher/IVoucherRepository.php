<?php
namespace App\Repositories\Voucher;

use App\User;
use App\Merchant;
use App\Voucher;
use App\VoucherLimit;

interface IVoucherRepository {
    /**
     * List all vouchers
     * @return array [Voucher]
     */
    public function list($data, $paginate = false);

    /**
     * List all available vouchers
     * @return array [Voucher]
     */
    public function listAvailable($data, $paginate = false);


    /**
     * List all merchant's active vouchers
     * @param Merchant $merchant
     * @param boolean $paginate
     * @return array [Voucher]
     */
    public function listMerchantsActive(Merchant $merchant, $paginate = false);

    /**
     * List all merchant's ended vouchers
     * @param User $user
     * @param boolean $paginate
     * @return array [Voucher]
     */
    public function listMerchantsInactive(Merchant $merchant, $paginate = false);

    /**
     * Create Voucher
     * @param array $data
     * @param [UploadedFile] $files
     * @return Voucher $voucher
     */
    public function create($data, $files);

    /**
     * Update Voucher
     * @param Voucher $voucher
     * @param array $data
     * @param [UploadedFile] $files
     * @return Voucher $voucher
     */
    public function update(Voucher $voucher, $data, $files);

    /**
     * Update voucher merchants
     * @param Voucher $voucher
     * @param array $data
     * @return void
     */
    public function updateMerchants(Voucher $voucher, $data);

    /**
     * Delete Voucher
     * @param Voucher $voucher
     * @param boolean $forceDelete
     * @return null
     */
    public function delete(Voucher $voucher, $forceDelete = false);

    /**
     * Redeem voucher
     * @param Voucher $voucher
     * @param User $user
     * @return MyVoucher 
     */
    public function redeem(Voucher $voucher, User $user);

    /**
     * Checks if total limit has been reached
     * @param VoucherLimit $limit
     * @return boolean 
     */
    public function hasReachedTotalLimit(VoucherLimit $limit);

    /**
     * Checks if daily limit has been reached
     * @param VoucherLimit $limit
     * @return boolean 
     */
    public function hasReachedDailyLimit(VoucherLimit $limit);

    /**
     * Checks if user has reached per day limit
     * @param User $user
     * @param VoucherLimit $limit
     * @return boolean 
     */
    public function hasReachedPerDayLimit(User $user, VoucherLimit $limit);

    /**
     * Checks if user has reached person limit
     * @param User $user
     * @param VoucherLimit $limit
     * @return boolean 
     */
    public function hasReachedPersonLimit(User $user, VoucherLimit $limit);
}