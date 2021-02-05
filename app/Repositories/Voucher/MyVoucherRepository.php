<?php
namespace App\Repositories\Voucher;

use DB;
use App\User;
use App\MyVoucher;
use App\VoucherTransaction;
use Carbon\Carbon;

class MyVoucherRepository implements IMyVoucherRepository {

    public function listActive(User $user, $paginate = false) {
        $query = MyVoucher::join('merchants', 'merchants.id', '=', 'myvouchers.merchant_id')
            ->join('vouchers', 'vouchers.id', '=', 'myvouchers.voucher_id')
            ->where('myvouchers.user_id', $user->id)
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

    public function find($id) {
        return MyVoucher::find($id);
    }

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
}