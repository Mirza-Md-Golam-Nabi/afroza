<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    public function activeAll(){
        return $this->where('status', 1)->orderBy('product_name', 'asc')->get();
    }
}
