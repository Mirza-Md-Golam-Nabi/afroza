<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = "types";

    public function category(){
        return $this->hasMany('App\Model\Category', 'type_id', 'id');
    }

    public function product(){
        return $this->hasMany('App\Model\Product', 'type_id', 'id');
    }

    public function stock(){
        return $this->hasMany('App\Model\Stock', 'type_id', 'id');
    }
}
