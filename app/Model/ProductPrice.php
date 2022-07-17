<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = "product_price";

    public function product(){
        return $this->belongsTo('App\Model\Product', 'product_id', 'id');
    }

    public function productPriceSum(){
        return $this->where('status', 0)->sum('price');
    }

    public function activeProduct($product_id){
        return $this->where('product_id', $product_id)->where('status', 1)->first();
    }

    public function productHas($product_id){
        return $this->activeProduct($product_id) ? true : false;
    }
}
