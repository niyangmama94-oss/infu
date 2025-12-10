<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ServiceTag extends Model
{
  
    public function tagName(){
        return $this->belongsTo(Tag::class,'tag_id');
    }
}
