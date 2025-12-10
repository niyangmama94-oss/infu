<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{


    public function influencer()
    {
        return $this->belongsTo(Influencer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'order_id')->latestOfMany();
    }

    public function orderMessage()
    {
        return $this->hasMany(OrderConversation::class, 'order_id')->latest();
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

        if ($this->status == Status::ORDER_PENDING) {
            $html = '<span class="badge badge--secondary">' . trans('Pending') . '</span>';
        } elseif ($this->status == Status::ORDER_COMPLETED) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->status == Status::ORDER_INPROGRESS) {
            $html = '<span class="badge badge--primary">' . trans('Inprogress') . '</span>';
        } elseif ($this->status == Status::ORDER_DELIVERED) {
            $html = '<span class="badge badge--info">' . trans('Job Done') . '</span>';
        } elseif ($this->status == Status::ORDER_REPORTED) {
            $html = '<span class="badge badge--warning">' . trans('Reported') . '</span>';
        } elseif ($this->status == Status::ORDER_CANCELLED) {
            $html = '<span class="badge badge--dark">' . trans('Cancelled') . '</span>';
        } elseif ($this->status == Status::ORDER_REJECTED) {
            $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
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
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_COMPLETED);
    }

    public function scopeInCompleted($query)
    {
        return $query->where('status', '!=', 1);
    }

    public function scopeInprogress($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_INPROGRESS);
    }

    public function scopeJobDone($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_DELIVERED);
    }

    public function scopeReported($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_REPORTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_CANCELLED);
    }
    public function scopeRejected($query)
    {
        return $query->where('payment_status', Status::PAYMENT_SUCCESS)->where('status', Status::ORDER_REJECTED);
    }
}
