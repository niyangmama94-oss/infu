<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{


    protected $casts = [
        'key_points' => 'object',
    ];

    public function influencer(){
        return $this->belongsTo(Influencer::class,'influencer_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function gallery(){
        return $this->hasMany(ServiceGallery::class,'service_id');
    }

    public function completeOrder(){
        return $this->hasMany(Order::class,'service_id')->where('status',1);
    }

    public function totalOrder(){
        return $this->hasMany(Order::class,'service_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'service_tags');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'service_id');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get:fn () => $this->badgeData(),
        );
    }

    public function badgeData(){
        $html = '';
        if($this->status == Status::SERVICE_PENDING){
            $html = '<span class="badge badge--warning">'.trans('Pending').'</span>';
        }elseif($this->status == Status::SERVICE_APPROVED){
            $html = '<span><span class="badge badge--success">'.trans('Approved').'</span><br></span>';
        }elseif($this->status == Status::SERVICE_REJECTED){
            $html = '<span><span class="badge badge--danger">'.trans('Rejected').'</span><br></span>';
        }
        return $html;
    }

    public function scopePending()
    {
        return $this->where('status', Status::SERVICE_PENDING);
    }

    public function scopeApproved()
    {
        return $this->where('status', Status::SERVICE_APPROVED)->whereHas('category',function($query){
            $query->where('status',Status::ENABLE);
        })->whereHas('influencer',function($influencer){
            $influencer->where('status',Status::ENABLE);
        });

    }

    public function scopeRejected()
    {
        return $this->where('status', Status::SERVICE_REJECTED);
    }

    public function scopeAvailable()
    {
        return $this->whereIn('status', [Status::SERVICE_PENDING, Status::SERVICE_APPROVED]);
    }
}
