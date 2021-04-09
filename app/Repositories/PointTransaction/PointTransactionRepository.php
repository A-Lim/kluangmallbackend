<?php
namespace App\Repositories\PointTransaction;

use DB;
use App\User;
use App\PointTransaction;
use App\Receipt;
use Illuminate\Database\Eloquent\Builder;

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
            $user->update(['points' => $user->points - $pointTransaction->amount]);

        return $user->points;
    }

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = PointTransaction::buildQuery($data)
            ->orderBy('id', 'desc');
        
        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function creditPending() {
        DB::beginTransaction();
        $transactions = PointTransaction::where('type', PointTransaction::TYPE_PENDING)
            ->get();

        $user_ids = $transactions->pluck('user_id');
        $users = User::whereIn('id', $user_ids->toArray())->get();

        foreach ($transactions as $transaction) {
            $user = $users->where('id', $transaction->user_id)->first();
            $user->points = $user->points + $transaction->amount;
        }

        foreach ($users as $user) {
            $user->save();
        }

        PointTransaction::where('type', PointTransaction::TYPE_PENDING)
            ->update(['type' => PointTransaction::TYPE_ADD]);

        DB::commit();
    }

    private function queryWhere(Builder $query, $data, $key, $isDate = false) {
        $filterData = explode(':', $data);

        if (count($filterData) > 1 && $filterData[0] == 'contains' && $isDate)
            $query = $query->whereDate($key, 'LIKE', '%'.$filterData[1].'%');
        else if (count($filterData) > 1 && $filterData[0] == 'contains' && !$isDate)
            $query = $query->where($key, 'LIKE', '%'.$filterData[1].'%');
        else if (count($filterData) > 1 && $filterData[0] == 'equals' && $isDate)
            $query = $query->whereDate($key, $filterData[1]);
        else if (count($filterData) > 1 && $filterData[0] == 'equals' && !$isDate)
            $query = $query->where($key, $filterData[1]);
        else 
            $query = $query->where($key, $filterData[0]);

        return $query;
    }
}