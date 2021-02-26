<?php
namespace App\Repositories\Voucher;

use DB;
use App\User;
use App\Merchant;
use App\MyVoucher;
use App\Voucher;
use App\VoucherLimit;
use App\VoucherTransaction;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    public function listAvailable($data, $paginate = false) {
        $today = Carbon::today();
        $query = Voucher::join('merchants', 'merchants.id', '=', 'vouchers.merchant_id')
            ->join('merchant_categories', 'merchant_categories.id', '=', 'merchants.merchant_category_id')
            ->whereDate('vouchers.fromDate', '<=', $today)
            ->whereDate('vouchers.toDate', '>=', $today)
            ->select('vouchers.id', 'vouchers.name', 'merchants.id as merchant_id', 'merchants.name as merchant_name', 
                'merchant_categories.name as category', 'merchant_categories.id as category_id',
                'vouchers.image', 'vouchers.points', 'vouchers.description', 'vouchers.terms_and_conditions');

        if (isset($data['category_id']))
            $query->where('merchant_categories.id', $data['category_id']);
        
        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function listMerchantsActive(Merchant $merchant, $paginate = false) {
        $today = Carbon::today();
        $query = Voucher::where('vouchers.merchant_id', $merchant->id)
            ->where('vouchers.status', Voucher::STATUS_ACTIVE)
            ->whereDate('vouchers.toDate', '>=', $today)
            ->select('vouchers.*', 
                DB::raw('(select count(*) FROM voucher_transactions where voucher_transactions.voucher_id = vouchers.id AND voucher_transactions.type = \''.VoucherTransaction::TYPE_REDEEM.'\') as redeemed_count'),
                DB::raw('(select voucher_limits.value FROM voucher_limits where voucher_limits.voucher_id = vouchers.id AND voucher_limits.type = \''.VoucherLimit::TYPE_TOTAL.'\') as limit_count')
            );

        if ($paginate) {
            $limit = isset($data['limit']) ? $data['limit'] : 10;
            return $query->paginate($limit);
        }

        return $query->get();
    }

    /**
     * {@inheritdoc}
     */
    public function listMerchantsInactive(Merchant $merchant, $paginate = false) {
        $today = Carbon::today();
        $query = Voucher::where('merchant_id', $merchant->id)
            ->whereDate('toDate', '<', $today)
            ->select('vouchers.*', 
                DB::raw('(select count(*) FROM voucher_transactions where voucher_transactions.voucher_id = vouchers.id AND voucher_transactions.type = \''.VoucherTransaction::TYPE_REDEEM.'\') as redeemed_count'),
                DB::raw('(select voucher_limits.value FROM voucher_limits where voucher_limits.voucher_id = vouchers.id AND voucher_limits.type = \''.VoucherLimit::TYPE_TOTAL.'\') as limit_count')
            );

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
            ->select('vouchers.*', 
                DB::raw('(select count(*) FROM voucher_transactions where voucher_transactions.voucher_id = vouchers.id AND voucher_transactions.type = \''.VoucherTransaction::TYPE_REDEEM.'\') as redeemed_count'),
                DB::raw('(select voucher_limits.value FROM voucher_limits where voucher_limits.voucher_id = vouchers.id AND voucher_limits.type = \''.VoucherLimit::TYPE_TOTAL.'\') as limit_count')
            )->first();
    }

    public function rewardDetail(Voucher $voucher) {
        return Voucher::join('merchants', 'merchants.id', '=', 'vouchers.merchant_id')
            ->where('vouchers.id', $voucher->id)
            ->select('vouchers.id', 'vouchers.name', 'merchants.id as merchant_id', 'merchants.name as merchant_name', 'vouchers.image', 
                'vouchers.points', 'vouchers.description', 'vouchers.terms_and_conditions')
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create($data, $files) {
        $data['fromDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['fromDate']);
        $data['toDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['toDate']);
        $data['created_by'] = auth()->id();

        DB::beginTransaction();
        $voucher = Voucher::create($data);

        if (isset($files['uploadQr'])) {
            $voucher->qr = json_encode($this->saveQr($voucher, $files['uploadQr']));
            $voucher->save();
        }

        if (isset($files['uploadImage'])) {
            $voucher->image = json_encode($this->saveQr($voucher, $files['uploadImage']));
            $voucher->save();
        }

        if (isset($data['limits']))
            $voucher->limits()->createMany($data['limits']);

        DB::commit();
        
        return $voucher;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Voucher $voucher, $data, $files) {
        $data['fromDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['fromDate']);
        $data['toDate'] = Carbon::createFromFormat(env('DATE_FORMAT'), $data['toDate']);
        $data['updated_by'] = auth()->id();

        // prevent qr data to be filled into voucher object
        $qrData = @$data['qr'];
        unset($data['qr']);
        // prevent image data to be filled into voucher object
        $imageData = @$data['image'];
        unset($data['image']);

        $voucher->fill($data);

        DB::beginTransaction();
        // clear existing limits
        $voucher->limits()->delete();

        // update qr code
        if (isset($files['uploadQr'])) {
            $this->deleteQr($voucher);
            $voucher->qr = json_encode($this->saveQr($voucher, $files['uploadQr']));
        } else if (!isset($files['uploadQr']) && !$qrData) {
            $this->deleteQr($voucher);
            $voucher->qr = null;
            $voucher->data = null;
        } else {
            $data['qr'] = $voucher->getAttributes()['qr'];
        }

        // update image 
        if (isset($files['uploadImage'])) {
            $this->deleteImage($voucher);
            $voucher->image = json_encode($this->saveImage($voucher, $files['uploadImage']));
        } else if (!isset($files['uploadImage']) && !$imageData) {
            $this->deleteImage($voucher);
            $voucher->image = null;
        } else {
            $data['image'] = $voucher->getAttributes()['image'];
        }

        if (isset($data['limits']))
            $voucher->limits()->createMany($data['limits']);

        $voucher->save();
        DB::commit();
        return $voucher;
    }

    /**
     * {@inheritdoc}
     */
    public function updateExpired() {
        $today = Carbon::today();
        Voucher::whereDate('toDate', '<', $today)
            ->where('status', Voucher::STATUS_ACTIVE)
            ->update(['status' => Voucher::STATUS_EXPIRED]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Voucher $voucher, $forceDelete = false) {
        if ($forceDelete) {
            $voucher->forceDelete();
        } else {
            $data['updated_by'] = auth()->id();
            $data['deleted_at'] = Carbon::now();
            $voucher->fill($data);
            $voucher->save();
            // delete files
            $folderDir = 'vouchers/'.$voucher->id.'/';
            Storage::disk('s3')->deleteDirectory($folderDir);
        }
    }

    /**
     * {@inheritdoc}
     */
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
        
        return $myVoucher;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReachedTotalLimit(VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->count();

        return $count >= $limit->value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReachedDailyLimit(VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count >= $limit->value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReachedPerDayLimit(User $user, VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('user_id', $user->id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $count >= $limit->value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReachedPersonLimit(User $user, VoucherLimit $limit) {
        $count = VoucherTransaction::where('voucher_id', $limit->voucher_id)
            ->where('user_id', $user->id)
            ->where('type', VoucherTransaction::TYPE_REDEEM)
            ->count();
        
        return $count >= $limit->value;
    }

    private function saveQr(Voucher $voucher, UploadedFile $file) {
        $saveDirectory = 'vouchers/'.$voucher->id.'/qr/';

        $fileName = $file->getClientOriginalName();
        Storage::disk('s3')->putFileAs($saveDirectory, $file, $fileName, 'public');

        $data['name'] = $fileName;
        $data['path'] = Storage::disk('s3')->url($saveDirectory.$fileName);
        return $data;
    }

    private function saveImage(Voucher $voucher, UploadedFile $file) {
        $saveDirectory = 'vouchers/'.$voucher->id.'/';

        $fileName = $file->getClientOriginalName();
        Storage::disk('s3')->putFileAs($saveDirectory, $file, $fileName, 'public');

        $data['name'] = $fileName;
        $data['path'] = Storage::disk('s3')->url($saveDirectory.$fileName);
        return $data;
    }

    private function deleteQr(Voucher $voucher) {
        $qrOriginal = json_decode($voucher->getAttributes()['qr']);

        if ($qrOriginal != null)
            Storage::disk('s3')->delete('vouchers/'.$voucher->id.'/qr/'.$qrOriginal->name);
    }

    private function deleteImage(Voucher $voucher) {
        $imageOriginal = json_decode($voucher->getAttributes()['image']);

        if ($imageOriginal != null)
            Storage::disk('s3')->delete('vouchers/'.$voucher->id.'/'.$imageOriginal->name);
    }
}