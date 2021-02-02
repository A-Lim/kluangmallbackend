<?php
namespace App\Repositories\PointTransaction;

use App\User;
use App\PointTransaction;
use App\Receipt;

class PointTransactionRepository implements IPointTransactionRepository {

    /**
     * {@inheritdoc}
     */
    public function create(User $user, $data, Receipt $receipt = null) {
        $pointTransaction =  PointTransaction::create([
            'user_id' => $user->id,
            'receipt_id' => @$receipt->id,
            'amount' => $data['amount'],
            'type' => $data['type'],
            'description' => $data['description']
        ]);

        if ($data['type'] == PointTransaction::TYPE_ADD)
            $user->update(['points' => $user->points + $pointTransaction->amount]);
        
        if ($data['type'] == PointTransaction::TYPE_DEDUCT)
            $user->update(['points' => $user->points + $pointTransaction->amount]);

    }

    /**
     * {@inheritdoc}
     */
    public function list(User $user, $paginate = false) {
        $query = PointTransaction::where('user_id', $user->id)
            ->orderBy('id', 'desc');
        
        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }
}