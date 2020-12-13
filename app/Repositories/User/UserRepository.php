<?php
namespace App\Repositories\User;

use DB;
use App\User;
use App\UserGroup;
use App\Permission;
use App\Merchant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

use App\Helpers\ImageProcessor;

class UserRepository implements IUserRepository {

    public function permissions(User $user) {
        $userGroups = UserGroup::whereHas('users', function($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        })->get();
        
        
        $isAdmins = $userGroups->pluck('is_admin')->all();
        // check if there is an admin usergroups among the user's usergroup
        $hasAdmin = in_array(true, $isAdmins);

        // is has admin usergroup, return all permissions
        if ($hasAdmin)
            return Permission::pluck('code');

        $userGroupIds = $userGroups->pluck('id')->all();
        $permissions = Permission::whereHas('userGroups', function ($query) use ($userGroupIds) {
            $query->whereIn('usergroup_id', $userGroupIds);
        })->get()->pluck('code')->all();

        return $permissions;
    }
    
    /**
     * {@inheritdoc}
     */
    public function list($data, $paginate = false) {
        $query = User::buildQuery($data);

        if (isset($data['id']) && is_array($data['id'])) {
            $ids = implode(',', $data['id']);
            $query->orderByRaw(DB::raw("FIELD(id,".$ids.") DESC"));
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
    public function listMerchantUsers(Merchant $merchant, $data, $paginate = false) {
        $query = null;
        
        if ($data)
            $query = User::buildQuery($data);
        else 
            $query = User::query();

        $query->join('merchant_user', 'merchant_user.user_id', '=', 'users.id')
            ->where('merchant_user.merchant_id', $merchant->id);

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
        return User::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findWithUserGroups($id) {
        return User::with('usergroups')->where('id', $id)->firstOrFail();
    }

    /**
     * {@inheritdoc}
     */
    public function searchForOne($params) {
        return User::where($params)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create($data) {
        // prevent member_no from being altered 
        unset($data['member_no']);

        $data['password'] = Hash::make($data['password']);
        $data['member_no'] = $this->generateMemberNo();
        return User::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(User $user, $data) {
        // prevent member_no from being altered 
        unset($data['member_no']);

        if (!empty($data['password']))
            $data['password'] = Hash::make($data['password']);

        if (!empty($data['usergroups']))
            $user->userGroups()->sync($data['usergroups']);

        if (!empty($data['date_of_birth']))
            $data['date_of_birth'] = Carbon::parse($data['date_of_birth']);

        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(User $user, $newPassword) {
        $data['password'] = Hash::make($newPassword);
        $user->fill($data);
        $user->save();
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function resetPassword(User $user, $password) {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->otp_token = null;
        $user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAvatar(User $user, UploadedFile $file) {
        $imagePaths = [];

        $image = new ImageProcessor($file, 'users/avatar', $user->id);
        $imagePaths['normal'] = $image->fit(400, 400)->save();

        $image = new ImageProcessor($file, 'users/avatar', $user->id);
        $imagePaths['placeholder'] = $image->placeholder(400, 400)->save();

        $image = new ImageProcessor($file, 'users/avatar', $user->id);
        $imagePaths['thumbnail'] = $image->thumbnail()->save();

        $user->avatar = json_encode($imagePaths);
        $user->save();

        return $user->avatar;
    }

    /**
     * {@inheritdoc}
     */
    public function saveAvatarBasic(User $user, UploadedFile $file) {
        $image = new ImageProcessor($file, 'users/avatar', $user->id);
        $imagePath = $image->saveBasic();

        $user->avatar = $imagePath;
        $user->save();

        return $user->avatar;
    }

    /**
     * {@inheritdoc}
     */
    public function randomizePassword(User $user) {
        $random_password = Str::random(8);
        $user->password = Hash::make($random_password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        return $random_password;
    }

    /**
     * {@inheritdoc}
     */
    public function generateOtp(User $user) {
        $expiry = Carbon::now()->addMinutes(5);
        $user->otp = mt_rand(100000, 999999);
        $user->otp_expiry = $expiry;
        $user->save();

        return $user->otp;
    }

    /**
     * {@inheritdoc}
     */
    public function generateOtpToken(User $user) {
        $user->otp = null;
        $user->otp_expiry = null;
        $user->otp_token = md5(Str::random(10));
        $user->save();

        return $user->otp_token;
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