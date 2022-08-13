<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Dhaka');
    }

    public function lastUpdate($inTime, $outTime){
        if($inTime && $outTime){
            $lastUpdate = $inTime > $outTime ? $inTime : $outTime;
        }elseif($inTime){
            $lastUpdate = $inTime;
        }elseif($outTime){
            $lastUpdate = $outTime;
        }else{
            $lastUpdate = NULL;
        }
        return $lastUpdate;
    }

    public function dateFormatting($data){
        foreach($data as $key=>$value){
            $data[$key]['date'] = SessionController::date_reverse_short($value['date']);
        }
        return $data;
    }

    public function dateSorting($data){
        usort($data, function ($object1, $object2) {
            return $object1['date'] < $object2['date'];
        });
        return $data;
    }
}
