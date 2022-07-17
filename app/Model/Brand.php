<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = "brands";

    public function product(){
        return $this->hasMany('App\Model\Product', 'brand_id', 'id');
    }

    public function stock(){
        return $this->hasMany('App\Model\Stock', 'brand_id', 'id');
    }
}
