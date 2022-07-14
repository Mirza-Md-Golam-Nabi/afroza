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
}
