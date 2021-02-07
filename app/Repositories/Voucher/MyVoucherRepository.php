<?php
namespace App\Repositories\Voucher;

use DB;
use App\User;
use App\MyVoucher;
use App\VoucherTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class MyVoucherRepository implements IMyVoucherRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = MyVoucher::join('vouchers', 'vouchers.id', '=', 'myvouchers.voucher_id')
            ->join('merchants', 'merchants.id', '=', 'myvouchers.merchant_id')
            ->select('myvouchers.id', 'myvouchers.user_id', 'myvouchers.expiry_date', 'myvouchers.status',
                'vouchers.name', 'merchants.name as merchant');

        if (isset($data['name']))
            $this->queryWhere($query, $data['name'], 'vouchers.name');

        if (isset($data['user_id']))
            $this->queryWhere($query, $data['user_id'], 'myvouchers.user_id');
    
        if (isset($data['merchant']))
            $this->queryWhere($query, $data['merchant'], 'merchants.name');

        if (isset($data['expiry_date']))
            $this->queryWhere($query, $data['expiry_date'], 'myvouchers.expiry_date', true);
        
        if (isset($data['status']))
            $this->queryWhere($query, $data['status'], 'myvouchers.status');

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
        $query = MyVoucher::join('merchants', 'merchants.id', '=', 'myvouchers.merchant_id')
            ->join('vouchers', 'vouchers.id', '=', 'myvouchers.voucher_id')
            ->where('myvouchers.user_id', $user->id)
            ->where('myvouchers.status', MyVoucher::STATUS_ACTIVE)
            ->select('myvouchers.id', 'myvouchers.expiry_date', 'myvouchers.status',
                'merchants.name as merchant_name', 'merchants.id as merchant_id',
                'vouchers.name', 'vouchers.description', 'vouchers.image', 'vouchers.terms_and_conditions',
                DB::raw('(CASE WHEN vouchers.data IS NOT NULL THEN 1 ELSE 0 END) AS custom'))
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
    public function listInactive(User $user, $paginate = false) {
        $query = MyVoucher::join('merchants', 'merchants.id', '=', 'myvouchers.merchant_id')
            ->join('vouchers', 'vouchers.id', '=', 'myvouchers.voucher_id')
            ->where('myvouchers.user_id', $user->id)
            ->whereIn('myvouchers.status', [MyVoucher::STATUS_USED, MyVoucher::STATUS_EXPIRED])
            ->select('myvouchers.id', 'myvouchers.expiry_date', 'myvouchers.status',
                'merchants.name as merchant_name', 'merchants.id as merchant_id',
                'vouchers.name', 'vouchers.description', 'vouchers.image', 'vouchers.terms_and_conditions',
                DB::raw('(CASE WHEN vouchers.data IS NOT NULL THEN 1 ELSE 0 END) AS custom'))
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
        return MyVoucher::join('merchants', 'merchants.id', '=', 'myvouchers.merchant_id')
            ->join('vouchers', 'vouchers.id', '=', 'myvouchers.voucher_id')
            ->where('myvouchers.id', $id)
            ->select('myvouchers.id', 'myvouchers.expiry_date', 'myvouchers.status',
                'merchants.name as merchant_name', 'merchants.id as merchant_id',
                'vouchers.name', 'vouchers.description', 'vouchers.image', 'vouchers.terms_and_conditions',
                'vouchers.data',
                DB::raw('(CASE WHEN vouchers.data IS NOT NULL THEN 1 ELSE 0 END) AS custom'))
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function use(User $user, MyVoucher $myVoucher) {
        $myVoucher->status = MyVoucher::STATUS_USED;
        $myVoucher->save();

        // add transaction
        $transaction = VoucherTransaction::create([
            'myvoucher_id' => $myVoucher->id,
            'merchant_id' => $myVoucher->merchant_id,
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
            case 'name':
                return 'vouchers.name';
            
            case 'merchant':
                return 'merchants.name';

            case 'expiry_date':
                return 'myvouchers.expiry_date';

            case 'status':
                return 'myvouchers.status';
            
            default:
                return null;
        }
    }
}