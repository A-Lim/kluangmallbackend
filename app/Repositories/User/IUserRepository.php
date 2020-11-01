<?php
namespace App\Repositories\User;

use App\User;
use Illuminate\Http\UploadedFile;

interface IUserRepository
{

    /**
     * List users users
     * 
     * @param array $query
     * @param boolean $paginate = false
     * @return [User] / LengthAwarePaginator
     */
    public function list(array $query, $paginate = false);

    /**
     * Find user from id
     * 
     * @param integer $id
     * @return User
     */
    public function find($id);

    /**
     * Find user from id with usergroups
     * 
     * @param integer $id
     * @return User
     */
    public function findWithUserGroups($id);
    
    /**
     * Find user based on params
     * 
     * @param array $params
     * @return User
     */
    public function searchForOne($params);
    
    /**
     * Creates a user
     * 
     * @param array $data
     * @return User
     */
    public function create($data);

     /**
     * Update a user
     * 
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, $data);

    /**
     * Reset user password
     * 
     * @param User $user
     * @param string $password
     * @return void
     */
    public function resetPassword(User $user, $password);

    /**
     * Save user avatar
     * 
     * @param User $user
     * @return array ['normal' => ?, 'placeholder' => ?, 'thumbnail' => ?]
     */
    public function saveAvatar(User $user, UploadedFile $file);

    /**
     * Save user avatar
     * 
     * @param User $user
     * @return string
     */
    public function saveAvatarBasic(User $user, UploadedFile $file);

    /**
     * Generate a random password for user
     * 
     * @param User $user
     * @return string
     */
    public function randomizePassword(User $user);

    /**
     * Generate an OTP for user
     * 
     * @param User $user
     * @return string
     */
    public function generateOtp(User $user);

    /**
     * Generate an OTP token
     * 
     * @param User $user
     * @return string
     */
    public function generateOtpToken(User $user);
}