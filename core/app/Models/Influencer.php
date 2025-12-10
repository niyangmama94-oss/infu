<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\InfluencerNotify;
use Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Influencer extends Authenticatable {
    use InfluencerNotify;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data'          => 'object',
        'skills'            => 'object',
        'languages'         => 'array',
        'ver_code_send_at'  => 'datetime',
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, 'influencer_categories');
    }

    public function message() {
        return $this->hasMany(Conversation::class, 'influencer_id')->latest();
    }

    public function Hiring() {
        return $this->hasMany(Hiring::class, 'influencer_id');
    }

    public function Order() {
        return $this->hasMany(Order::class, 'influencer_id');
    }

    public function education() {
        return $this->hasMany(InfluencerEducation::class, 'influencer_id')->latest();
    }

    public function qualification() {
        return $this->hasMany(InfluencerQualification::class, 'influencer_id')->latest();
    }

    public function languages() {
        return $this->hasMany(InfluencerLanguage::class, 'influencer_id')->latest();
    }

    public function skills() {
        return $this->hasMany(InfluencerSkill::class, 'influencer_id');
    }

    public function services() {
        return $this->hasMany(Service::class, 'influencer_id')->where('status', Status::INFLUENCER_ACTIVE);
    }

    public function reviews() {
        return $this->hasMany(Review::class, 'influencer_id');
    }

    public function orderReviews() {
        return $this->hasMany(Review::class, 'influencer_id')->where('order_id', 0);
    }

    public function socialLink() {
        return $this->hasMany(SocialLink::class, 'influencer_id');
    }

    public function isOnline() {
        return Cache::has('last_seen' . $this->id);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get:fn() => $this->firstname . ' ' . $this->lastname,
        );
    }
    // SCOPES
    public function scopeActive($query) {
        return $query->where('status', Status::INFLUENCER_ACTIVE)->where('ev',Status::VERIFIED)->where('sv',Status::VERIFIED);
    }
    public function scopeBanned($query)
    {
        return $query->where('status', Status::INFLUENCER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance() {
        return $this->where('balance', '>', 0);
    }
}
