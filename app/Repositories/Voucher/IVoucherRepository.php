<?php
namespace App\Repositories\Voucher;

interface IVoucherRepository {
    /**
     * List all vouchers
     * @return array [Voucher]
     */
    public function list($data, $paginate = false);

    /**
     * Create Voucher
     * @return Voucher $voucher
     */
    public function create($data);
}