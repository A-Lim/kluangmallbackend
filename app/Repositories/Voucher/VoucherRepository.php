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
            $query = Voucher::with(['merchants'])->buildQuery($data);
        else 
            $query = Voucher::query()->orderBy('id', 'desc');

        // searching in join table
        if (isset($data['merchant_name'])) {
            $filterData = explode(':', $data['merchant_name']);
            $filterType = strtolower($filterData[0]);
            $filterVal  = $filterData[1];
            
            if ($filterType == 'contains')
                $query->whereHas('merchants', function ($q) use ($filterVal) {
                    $q->where('name', 'LIKE', '%'.$filterVal.'%');
                });
            
            if ($filterType == 'equals')
                $query->whereHas('merchants', function ($q) use ($filterVal) {
                    $query->where('merchants.name', $filterVal);
                });
        }
        
        $query->orderBy('vouchers.id', 'desc');
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
        $query = Voucher::with('merchants', 'merchants.category')
            ->whereDate('vouchers.fromDate', '<=', $today)
            ->whereDate('vouchers.toDate', '>=', $today)
            ->where('status', Voucher::STATUS_ACTIVE);

        if (isset($data['category_id'])) {
            $query->whereHas('merchants', function ($q) use ($data) {
                $q->where('merchant_category_id', $data['category_id']);
            });
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
    public function listMerchantsActive(Merchant $merchant, $paginate = false) {
        $today = Carbon::today();
        $query = Voucher::has('merchants', 1)
            ->whereHas('merchants', function ($q) use ($merchant) {
                $q->where('id', $merchant->id);
            })
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
        $query = Voucher::has('merchants', 1)
            ->whereHas('merchants', function ($q) use ($merchant) {
                $q->where('id', $merchant->id);
            })
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
        $voucher = Voucher::with(['limits'])
            ->where('id', $id)
            ->select('vouchers.*', 
                DB::raw('(select count(*) FROM voucher_transactions where voucher_transactions.voucher_id = vouchers.id AND voucher_transactions.type = \''.VoucherTransaction::TYPE_REDEEM.'\') as redeemed_count'),
                DB::raw('(select voucher_limits.value FROM voucher_limits where voucher_limits.voucher_id = vouchers.id AND voucher_limits.type = \''.VoucherLimit::TYPE_TOTAL.'\') as limit_count')
            )->first();

        $voucher->merchant_ids = $voucher->merchants()->pluck('id');
        return $voucher;
    }

    /**
     * {@inheritdoc}
     */
    public function rewardDetail(Voucher $voucher) {
        return Voucher::with('merchants', 'merchants.category')
            ->where('vouchers.id', $voucher->id)
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
    public function updateMerchants(Voucher $voucher, $data) {
        $voucher->merchants()->sync($data['merchant_ids']);
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
        $merchants = $voucher->merchants;
        $merchant_id = 0;

        if ($merchants->count() == 1)
            $merchant_id = $merchants->first()->id;

        $myVoucher = MyVoucher::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'expiry_date' => $voucher->toDate,
            'status' => MyVoucher::STATUS_ACTIVE
        ]);

        $transaction = VoucherTransaction::create([
            'myvoucher_id' => $myVoucher->id,
            'merchant_id' => $merchant_id,
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