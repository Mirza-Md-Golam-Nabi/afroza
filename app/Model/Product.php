<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    public function type(){
        return $this->belongsTo('App\Model\Type', 'type_id', 'id');
    }

    public function category(){
        return $this->belongsTo('App\Model\Category', 'category_id', 'id');
    }

    public function brand(){
        return $this->belongsTo('App\Model\Brand', 'brand_id', 'id');
    }

    public function productPrice(){
        return $this->hasMany('App\Model\ProductPrice', 'product_id', 'id');
    }

    public function stock(){
        return $this->hasOne('App\Model\Stock', 'product_id', 'id');
    }

    public function stockIn(){
        return $this->hasMany('App\Model\Stockin', 'product_id', 'id');
    }

    public function stockOut(){
        return $this->hasMany('App\Model\Stockout', 'product_id', 'id');
    }

    public function activeAll(){
        return $this->where('status', 1)->orderBy('product_name', 'asc')->get();
    }
}
