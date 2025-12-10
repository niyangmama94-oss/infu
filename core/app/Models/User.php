<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Cache;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','ver_code','balance','kyc_data'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }

    public function message(){
        return $this->hasMany(Conversation::class,'user_id')->latest();
    }
    public function orderCompleted(){
        return $this->hasMany(Order::class,'user_id')->where('status',1);
    }

    public function isUserOnline()
    {
        return Cache::has('user_last_seen' . $this->id);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', Status::USER_ACTIVE);
    }

    public function scopeBanned()
    {
        return $this->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified()
    {
        return $this->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending()
    {
        return $this->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified()
    {
        return $this->where('ev', Status::KYC_VERIFIED);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance()
    {
        return $this->where('balance','>', 0);
    }

}
