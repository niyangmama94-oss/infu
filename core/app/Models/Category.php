<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use GlobalStatus;


    public function influencers()
    {
        return $this->belongsToMany(Influencer::class, 'influencer_categories');
    }

    public function service(){
        return $this->hasMany(Service::class,'category_id');
    }

    public function scopeActive() {
        return $this->where('status', 1);
    }
}
