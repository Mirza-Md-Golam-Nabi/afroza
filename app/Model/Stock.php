<?php

namespace App\model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = "stock";

    public function productPriceSum(){
        return $this->where('applicable_stock', '!=', 0)->sum(DB::raw('applicable_stock * current_price'));
    }

    public function currentProduct($product_id){
        return $this->where('product_id', $product_id)->first();
    }

    public function currentAll(){
        return $this->select('product_id','product_name', 'quantity', 'current_price')
            ->where('status', 1)
            ->orderBy('product_name', 'asc')
            ->get();
    }
}
