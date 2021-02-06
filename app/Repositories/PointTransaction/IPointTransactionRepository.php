<?php
namespace App\Repositories\PointTransaction;

use App\User;
use App\Receipt;

interface IPointTransactionRepository {
    /**
     * Create points transaction
     * @param User $user
     * @param array $data
     * @param Receipt $receipt
     * @return PointTransaction
     */
    public function create(User $user, $data, Receipt $receipt = null);

    /**
     * List all points transaction
     * @param array $data
     * @param boolean $paginate
     * @return PointTransaction
     */
    public function list($data, $paginate = false);

    /**
     * Credit pending points
     * @return void
     */
    public function creditPending();
}