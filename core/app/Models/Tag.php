<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    public function serviceTag(){
        return $this->hasMany(ServiceTag::class);
    }
}
