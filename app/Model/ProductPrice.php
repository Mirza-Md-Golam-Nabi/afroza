<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $table = "product_price";

    public function productPriceSum(){
        return $this->where('status', 0)->sum('price');
    }
}
