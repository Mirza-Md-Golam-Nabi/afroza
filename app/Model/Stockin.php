<?php

namespace App\model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stockin extends Model
{
    protected $table = "stockin_history";

    public function product(){
        return $this->belongsTo('App\Model\Product', 'product_id', 'id');
    }

    public function singleProduct($product_id){
        return $this->select(
                        'date',
                        DB::raw('SUM(quantity) AS stockin')
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
