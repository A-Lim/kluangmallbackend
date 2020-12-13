<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\URL;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\MerchantWelcome;

use App\Http\Traits\HasUserGroups;
use App\Http\Traits\CustomQuery;
use App\Casts\Json;

class User extends Authenticatable {
    use Notifiable, HasApiTokens, HasUserGroups, CustomQuery;

    protected $fillable = ['name', 'email', 'password', 'member_no', 'phone', 'gender', 'date_of_birth', 'avatar', 'email_verified_at', 'status', 'otp', 'otp_token', 'otp_expiry'];
    protected $hidden = ['password', 'otp', 'otp_token', 'otp_expiry', 'remember_token', 'created_at', 'updated_at'];
    protected $casts = [
        'email_verified_at' => 'datetime:d M Y',
        'date_of_birth' => 'datetime:d M Y',
    ];

    // list of properties queryable for datatable
    public static $queryable = ['name', 'email', 'member_no', 'phone', 'gender', 'date_of_birth', 'status'];

    const STATUS_ACTIVE = 'active';
    const STATUS_LOCKED = 'locked';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_UNVERIFIED = 'unverified';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_LOCKED,
        self::STATUS_UNVERIFIED,
        self::STATUS_INACTIVE,
    ];

    public function merchants() {
        return $this->belongsToMany(Merchant::class, 'merchant_user', 'merchant_id', 'user_id'); 
    }

    /**
     * Model events
     *
     * @return void
     */
    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            // if status is not provided
            // set default to unverified
            if (empty($model->status)) {
                $model->status = self::STATUS_UNVERIFIED;
            }
        });
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new CustomVerifyEmail($this->email));
    }

    /**
     * Send merchant accou
     *
     * @return void
     */
    public function sendMerchantWelcomeNotification($token) {
        $this->notify(new MerchantWelcome($token));
    }

    /**
     * Mark email as verified (email_verified_at, status)
     *
     * @return void
     */
    public function markEmailAsVerified() {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'status' => self::STATUS_ACTIVE,
        ])->save();
    }

    /**
     * Checks if user is verified
     *
     * @return boolean
     */
    public function hasVerifiedEmail() {
        return $this->status != self::STATUS_UNVERIFIED || $this->email_verified_at != null;
    }

    /**
     * Checks if otp is valid
     *
     * @return boolean
     */
    public function isOtpValid($otp) {
        return $this->otp == $otp && 
            Carbon::parse($this->otp_expiry)->greaterThan(Carbon::now());
    }

    /**
     * Checks if otp is valid
     *
     * @return boolean
     */
    public function isOtpTokenValid($otp_token) {
        return $this->otp_token == $otp_token;
    }

    /******** Accessors and Mutators ********/
    
    public function getAvatarAttribute($value) {
        if ($value != '' || $value != null)
            return URL::to('/').$value;
        else 
            return $value;
    }
}
