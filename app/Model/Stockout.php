<?php

namespace App\model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stockout extends Model
{
    protected $table = "stockout_history";

    public function singleProduct($product_id){
        return $this->select(
                        'date',
                        DB::raw('SUM(quantity) AS stockout'),
                        DB::raw('SUM(buying_price) AS buy'),
                        DB::raw('SUM(selling_price) AS sell')
                    )
                    ->where('product_id', $product_id)
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->get();
    }

    public function updateTimeForProduct($product_id){
        return $this->select('updated_at')->where('product_id', $product_id)->orderBy('id','desc')->first();
    }

    public function updateTimeForAll($date){
        return $this->select('updated_at')->where('date', $date)->orderBy('id','desc')->first();
    }
}
