<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Model;

class Hiring extends Model
{


    public function influencer()
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'hiring_id')->latestOfMany();
    }

    public function hiringMessage()
    {
        return $this->hasMany(HiringConversation::class, 'hiring_id')->latest();
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == Status::HIRING_PENDING) {
            $html = '<span class="badge badge--secondary">' . trans('Pending') . '</span>';
        } elseif ($this->status == Status::HIRING_COMPLETED) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->status == Status::HIRING_INPROGRESS) {
            $html = '<span class="badge badge--primary">' . trans('Inprogress') . '</span>';
        } elseif ($this->status == Status::HIRING_DELIVERED) {
            $html = '<span class="badge badge--info">' . trans('Job Done') . '</span>';
        } elseif ($this->status == Status::HIRING_REPORTED) {
            $html = '<span class="badge badge--danger">' . trans('Reported') . '</span>';
        } elseif ($this->status == Status::HIRING_CANCELLED) {
            $html = '<span class="badge badge--dark">' . trans('Cancelled') . '</span>';
        } elseif ($this->status == Status::HIRING_REJECTED) {
            $html = '<span class="badge badge--warning">' . trans('Cancelled') . '</span>';
        }
        return $html;
    }

    // SCOPES
    public function scopePaymentCompleted($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_COMPLETED);
    }

    public function scopeInprogress($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_INPROGRESS);
    }

    public function scopeJobDone($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_DELIVERED);
    }

    public function scopeReported($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_REPORTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_CANCELLED);
    }
    public function scopeRejected($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::HIRING_REJECTED);
    }
}
