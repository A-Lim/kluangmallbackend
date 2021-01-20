<?php
namespace App\Repositories\Voucher;

use DB;
use App\User;
use App\MyVoucher;
use App\Voucher;
use App\VoucherLimit;
use App\VoucherTransaction;
use Carbon\Carbon;

class VoucherRepository implements IVoucherRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Voucher::with('limits')->buildQuery($data);
        else 
            $query = Voucher::query()->orderBy('id', 'desc');

        $query = $query->join('merchants' , 'merchants.id', '=', 'vouchers.merchant_id')
            ->select('vouchers.*', 'merchants.name as merchant_name');

        // searching in join table
        if (isset($data['merchant_name'])) {
            

            $filterData = explode(':', $data['merchant_name']);
            $filterType = strtolower($filterData[0]);
            $filterVal  = $filterData[1];

            if ($filterType == 'contains')
                $query->where('merchants.name', 'LIKE', '%'.$filterVal.'%');
            
            if ($filterType == 'equals')
                $query->where('merchants.name', $filterVal);
        }
        
        $query->orderBy('id', 'desc');
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
        return Voucher::with('limits')
            ->where('id', $id)
            ->first();
    }


    /**
     * {@inheritdoc}
     */
    public function create($data) {
        $data['fromDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['fromDate']);
        $data['toDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['toDate']);
        $data['created_by'] = auth()->id();

        DB::beginTransaction();
        $voucher = Voucher::create($data);

        if (isset($data['limits']))
            $voucher->limits()->createMany($data['limits']);

        DB::commit();
        
        return $voucher;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Voucher $voucher, $data) {
        $data['fromDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['fromDate']);
        $data['toDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['toDate']);
        $data['updated_by'] = auth()->id();

        $voucher->fill($data);

        // clear existing limits
        $voucher->limits()->delete();

        DB::beginTransaction();
        if (isset($data['limits']))
            $voucher->limits()->createMany($data['limits']);

        $voucher->save();
        DB::commit();

        return $voucher;
    }

    public function redeem(Voucher $voucher, User $user) {
        // add to user's voucher
        $myVoucher = MyVoucher::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'merchant_id' => $voucher->merchant_id,
            'expiry_date' => $voucher->toDate,
            'status' => MyVoucher::STATUS_ACTIVE
        ]);

        $transaction = VoucherTransaction::create([
            'myvoucher_id' => $myVoucher->id,
            'merchant_id' => $voucher->merchant_id,
            'user_id' => $user->id,
            'voucher_id' => $voucher->id,
            'type' => VoucherTransaction::TYPE_REDEEM
        ]);

        // deduct points
        $user->update(['points' => $user->points - $voucher->points]);
        return $myVoucher;
    }

    public function hasReachedTotalLimit(VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->count();

        return $count >= $limit->value;
    }

    public function hasReachedDailyLimit(VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count >= $limit->value;
    }

    public function hasReachedPerDayLimit(User $user, VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('user_id', $user->id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count >= $limit->value;
    }

    public function hasReachedPersonLimit(User $user, VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('user_id', $user->id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->count();
        
        return $count >= $limit->value;
    }
}