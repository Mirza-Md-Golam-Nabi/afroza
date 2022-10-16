<?php

namespace App\model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stockout extends Model
{
    protected $table = "stockout_history";

    public function product()
    {
        return $this->belongsTo('App\Model\Product', 'product_id', 'id');
    }

    public function singleProduct($product_id)
    {
        return $this->select(
            'date',
            DB::raw('SUM(quantity) AS stockout'),
            DB::raw('SUM(buying_price) AS buy'),
            DB::raw('SUM(selling_price) AS sell')
        )
            ->where('product_id', $product_id)
            ->orderBy('date', 'desc')
            ->groupBy('date')
            ->get();
    }

    public function singleProductDateWise($date, $product_id)
    {
        return $this->where([
            ['date', '=', $date],
            ['product_id', '=', $product_id],
        ])->get();
    }

    public function lastUpdateTimeForProduct($product_id)
    {
        return $this->where('product_id', $product_id)->latest('updated_at')->value('updated_at');
    }

    public function lastUpdateTimeForAll($date)
    {
        return $this->where('date', $date)->latest('updated_at')->value('updated_at');
    }

    public function dateWiseGroupProduct($date)
    {
        return $this->select(
            'product_id',
            DB::raw('SUM(quantity) as quantity'),
            DB::raw('SUM(buying_price) as buy'),
            DB::raw('SUM(selling_price) as sell')
        )
            ->where('date', $date)
            ->groupBy('product_id')
            ->get();
    }

    public function dateWiseAllProduct($date)
    {
        return $this->select(
            'product_id',
            'quantity',
            'buying_price as buy',
            'selling_price as sell'
        )
            ->where('date', $date)
            ->get();
    }

    public function deleteProductDateWise($date, $product_id)
    {
        return $this->where('date', $date)->where('product_id', $product_id)->delete();
    }
}
