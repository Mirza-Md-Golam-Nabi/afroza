<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Vitamin extends Model
{
    protected $table = 'vitamins';

    public function fruit(){
        return $this->belongsToMany('App\Model\Fruit', 'fruit_vitamin', 'vitamin_id', 'fruit_id')->wherePivot('status', 1)->withPivot('full_name');
    }
}
