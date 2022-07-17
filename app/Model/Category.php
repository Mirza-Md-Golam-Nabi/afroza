<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";

    public function type(){
        return $this->belongsTo('App\Model\Type', 'type_id', 'id');
    }

    public function product(){
        return $this->hasMany('App\Model\Product', 'category_id', 'id');
    }

    public function stock(){
        return $this->hasMany('App\Model\Stock', 'category_id', 'id');
    }
}
