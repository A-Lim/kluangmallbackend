<?php 
namespace App\Repositories\Voucher;

use App\User;
use App\Merchant;
use App\VoucherTransaction;
use Illuminate\Database\Eloquent\Builder;

class VoucherTransactionRepository implements IVoucherTransactionRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = VoucherTransaction::join('merchants', 'merchants.id', '=', 'voucher_transactions.merchant_id')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_transactions.voucher_id')
            ->join('users', 'users.id', '=', 'voucher_transactions.user_id')
            ->select('voucher_transactions.id', 'users.name as user', 'merchants.name as merchant', 'vouchers.name as voucher', 'voucher_transactions.type', 'voucher_transactions.created_at');

        // filter by user
        if (isset($data['user']))
            $this->queryWhere($query, $data['user'], 'users.name');

        // filter by merchant
        if (isset($data['merchant']))
            $this->queryWhere($query, $data['merchant'], 'merchants.name');

        // filter by vouchers
        if (isset($data['voucher']))
            $this->queryWhere($query, $data['voucher'], 'vouchers.name');

        // filter by type
        if (isset($data['type']))
            $this->queryWhere($query, $data['type'], 'voucher_transactions.type');

        // filter by created_at
        if (isset($data['created_at']))
            $this->queryWhereDate($query, $data['created_at'], 'voucher_transactions.created_at', true);

        // sort
        if (isset($data['sort'])) {
            $sortData = explode(';', $data['sort']);
            foreach($sortData as $sortDetail) {
                $sortData = explode(':', $sortDetail);
                if (count($sortData) < 2) {
                    // throw exception
                }
                $sortCol = $sortData[1];
                $sortType = $sortData[0];

                $sortKey = $this->getSortKey($sortCol);
                $query->orderBy($sortKey, $sortType);
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function listMy(User $user, $paginate = false) {
        $query = VoucherTransaction::join('merchants', 'merchants.id', '=', 'voucher_transactions.merchant_id')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_transactions.voucher_id')
            ->where('voucher_transactions.user_id', $user->id)
            ->select('voucher_transactions.id', 'merchants.name as merchant', 'vouchers.name as voucher', 'voucher_transactions.type', 'voucher_transactions.created_at');

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function redeemCount(Merchant $merchant) {
        return VoucherTransaction::where('merchant_id', $merchant->id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->count();
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

    private function getSortKey($column) {
        switch ($column) {
            case 'user':
                return 'users.name';
            
            case 'merchant':
                return 'merchants.name';

            case 'voucher':
                return 'vouchers.name';

            case 'type': 
            case 'created_at':
                return 'voucher_transactions.'.$column;
            
            default:
                return null;
        }
    }
}