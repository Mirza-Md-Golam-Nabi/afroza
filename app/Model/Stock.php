<?php

namespace App\model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = "stock";

    public function type()
    {
        return $this->belongsTo('App\Model\Type', 'type_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\Category', 'category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Model\Brand', 'brand_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Model\Product', 'product_id', 'id');
    }

    public function productPriceSum()
    {
        return $this->where('applicable_stock', '!=', 0)->sum(DB::raw('applicable_stock * current_price'));
    }

    public function currentProduct($product_id)
    {
        return $this->where('product_id', $product_id)->first();
    }

    public function currentAll()
    {
        return $this->select('product_id', 'product_name', 'quantity', 'current_price')
            ->where('status', 1)
            ->orderBy('product_name', 'asc')
            ->get();
    }

    public function availableProducts()
    {
        return $this->select('product_id', 'product_name')
            ->where('quantity', '>', 0)
            ->orderBy('product_name')
            ->get();
    }
}
