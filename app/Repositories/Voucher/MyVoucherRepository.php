<?php
namespace App\Repositories\Voucher;

use DB;
use App\User;
use App\Merchant;
use App\MyVoucher;
use App\VoucherTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class MyVoucherRepository implements IMyVoucherRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = MyVoucher::with('voucher')->whereHas('voucher', function ($q) {
            $q->where('deleted_at', null);
        });

        if (isset($data['name']))
            $query->whereHas('voucher', function ($q) use ($data) {
                $this->queryWhere($q, $data['name'], 'name');
            });
            

        if (isset($data['user_id']))
            $this->queryWhere($query, $data['user_id'], 'user_id');
    
        if (isset($data['merchant_name']))
            $query->whereHas('voucher.merchants', function ($q) use ($data) {
                $this->queryWhere($q, $data['merchant_name'], 'name');
            });

        if (isset($data['expiry_date']))
            $this->queryWhere($query, $data['expiry_date'], 'expiry_date', true);
        
        if (isset($data['status']))
            $this->queryWhere($query, $data['status'], 'status');

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
    public function listActive(User $user, $paginate = false) {
        $query = MyVoucher::with('voucher', 'voucher.merchants')
            ->whereHas('voucher', function ($q) {
                $q->where('deleted_at', null);
            })
            ->where('myvouchers.user_id', $user->id)
            ->where('myvouchers.status', MyVoucher::STATUS_ACTIVE)
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
    public function listInactive(User $user, $paginate = false) {
        $query = MyVoucher::with('voucher', 'voucher.merchants')
            ->whereHas('voucher', function ($q) {
                $q->where('deleted_at', null);
            })
            ->where('myvouchers.user_id', $user->id)
            ->whereIn('myvouchers.status', [MyVoucher::STATUS_USED, MyVoucher::STATUS_EXPIRED])
            ->orderBy('myvouchers.id', 'desc');

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id) {
        return MyVoucher::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function details($id) {
        return MyVoucher::with('voucher', 'voucher.merchants')
            ->where('myvouchers.id', $id)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function use(User $user, MyVoucher $myVoucher, Merchant $merchant = null) {
        $myVoucher->status = MyVoucher::STATUS_USED;
        $myVoucher->save();

        // take the first merchant from myVoucher
        if ($merchant == null)
            $merchant = $myVoucher->voucher->merchants->first();

        // add transaction
        $transaction = VoucherTransaction::create([
            'myvoucher_id' => $myVoucher->id,
            'merchant_id' => $merchant->id,
            'user_id' => $myVoucher->user_id,
            'voucher_id' => $myVoucher->voucher_id,
            'type' => VoucherTransaction::TYPE_USE
        ]);

        return $myVoucher;
    }

    /**
     * {@inheritdoc}
     */
    public function updateExpired() {
        $today = Carbon::today();
        MyVoucher::whereDate('expiry_date', '<', $today)
            ->where('status', MyVoucher::STATUS_ACTIVE)
            ->update(['status' => MyVoucher::STATUS_EXPIRED]);
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
            case 'expiry_date':
                return 'myvouchers.expiry_date';

            case 'status':
                return 'myvouchers.status';
            
            default:
                return null;
        }
    }
}