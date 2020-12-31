<?php
namespace App\Repositories\Merchant;

use DB;
use App\User;
use App\UserGroup;
use App\Merchant;
use App\MerchantVisit;
use App\MerchantCategory;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MerchantRepository implements IMerchantRepository {

    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = Merchant::with('account')->buildQuery($data);
        else 
            $query = Merchant::query()->orderBy('id', 'desc');

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
    public function listSimilar(Merchant $merchant) {
        return Merchant::where('category', $merchant->category)
            ->where('id', '<>', $merchant->id)
            ->orderByRaw('RAND()')
            ->limit(10)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function listCategories($data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = MerchantCategory::buildQuery($data);
        else 
            $query = MerchantCategory::query()->orderBy('name', 'asc');

        if (isset($data['name']) && is_array($data['name'])) {
            $names = implode("','", $data['name']);
            $query->orderByRaw(DB::raw("FIELD(name,'".$names."') DESC"))
                ->orderBy('name');
        } else {
            $query->orderBy('name');
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
    public function find($id, $withDetails) {
        if ($withDetails)
            return Merchant::with('account')->where('id', $id)->first();
        else
            return Merchant::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function track($data) {
        $exists = MerchantVisit::where([
            'merchant_id' => $data['merchant_id'],
            'device_id' => $data['device_id']
        ])->exists();
        
        if (!$exists)
            MerchantVisit::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function create($data, $files) {
        DB::beginTransaction();
        $data['created_by'] = auth()->id();
        $merchant = Merchant::create($data);

        $merchantCategory = MerchantCategory::firstOrCreate(['name' => $data['category']]);

        if (isset($files['uploadLogo'])) {
            $merchant->logo = json_encode($this->saveImage($merchant, $files['uploadLogo']));
        }

        $merchant->save();
        // create merchant account
        $merchant->account()->create();
        DB::commit();

        return $merchant;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Merchant $merchant, $data, $files) {
        DB::beginTransaction();

        $data['updated_by'] = auth()->id();

        if (isset($files['uploadLogo'])) {
            $this->deleteLogo($merchant);
            $data['logo'] = json_encode($this->saveImage($merchant, $files['uploadLogo']));
        } 
        
        if (!isset($data['logo'])) {
            $this->deleteLogo($merchant);
            $data['logo'] = null;
        } else {
            $data['logo'] = $merchant->getAttributes()['logo'];
        }

        $merchant->fill($data);
        $merchant->save();

        $merchantCategory = MerchantCategory::firstOrCreate(['name' => $data['category']]);

        // deactivate merchant users
        if ($merchant->status == Merchant::STATUS_INACTIVE) {
            $users = $merchant->users;
            User::whereIn('id', $users->pluck('id')->toArray())->update(['status' => User::STATUS_INACTIVE]);
        }

        DB::commit();
        return $merchant;
    }

    /**
     * {@inheritdoc}
     */
    public function createUsers(Merchant $merchant, $data) {
        DB::beginTransaction();
        $insert_data = [];
        $memberNos = [];

        foreach ($data['users'] as $user) {
            $user_data = $user;
            $password = Str::random(8);
            $user_data['password'] = Hash::make($password);
            $user_data['status'] = 'active';

            $memberNo = $this->generateMemberNo();
            while (in_array($memberNo, $memberNos)) {
                $memberNo = $this->generateMemberNo();
            }
            $user_data['member_no'] = $memberNo;
            array_push($insert_data, $user_data);
        }

        User::insert($insert_data);

        $emails = collect($insert_data)->pluck('email');
        $users = User::whereIn('email', $emails)->get();
        $merchantgroup = UserGroup::where('code', 'merchant')->first();

        $merchant_user_data = [];
        foreach ($users as $user) {
            array_push($merchant_user_data, [
            'merchant_id' => $merchant->id,
                'user_id' => $user->id
            ]);
        }

        $user_usergroup_data = [];
        foreach ($users as $user) {
            array_push($user_usergroup_data, [
                'usergroup_id' => $merchantgroup->id,
                'user_id' => $user->id
            ]);
        }

        DB::table('merchant_user')->insert($merchant_user_data);
        DB::table('user_usergroup')->insert($user_usergroup_data);
        DB::commit();
        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMerchantCategory(MerchantCategory $merchantCategory) {
        $merchantCategory->delete();
    }

    private function saveImage(Merchant $merchant, UploadedFile $file) {
        $saveDirectory = 'public/merchants/'.$merchant->id.'/logo/';

        $fileName = $file->getClientOriginalName();
        Storage::putFileAs($saveDirectory, $file, $fileName);

        $data['name'] = $fileName;
        $data['path'] = Storage::url($saveDirectory.$fileName);
        return $data;
    }

    private function deleteLogo(Merchant $merchant) {
        // image property without mutator
        $logoOriginal = json_decode($merchant->getAttributes()['logo']);

        if ($logoOriginal != null) {
            $fullPath = public_path($logoOriginal->path);
            if (file_exists($fullPath))
                unlink($fullPath);
        }
        $data['logo'] = null;
    }

    private function generateMemberNo() {
        $memberNo = mt_rand(1000000000, 9999999999);
        // check if member no exists
        while (User::where('member_no', $memberNo)->exists()) {
            $memberNo = mt_rand(1000000000, 9999999999);
        }

        return $memberNo;
    }
}