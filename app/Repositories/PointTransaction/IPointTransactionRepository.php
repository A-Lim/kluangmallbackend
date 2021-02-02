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
     * List points transaction
     * @return PointTransaction
     */
    public function list(User $user, $paginate = false);
}