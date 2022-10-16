<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Fruit extends Model
{
    protected $table = 'fruits';

    public function vitamin(){
        return $this->belongsToMany('App\Model\Vitamin', 'fruit_vitamin', 'fruit_id', 'vitamin_id');
    }
}
