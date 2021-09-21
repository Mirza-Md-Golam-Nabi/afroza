<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Device;

class TestController extends Controller
{
    public function test(){ 
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $mobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $userAgent);

        $deviceType = 2; // Laptop or Desktop
        if($mobile){
            $deviceType = 1; // Mobile
        }
        $flight = Device::updateOrCreate(
            ['devicetype'=>$deviceType, 'device' => $userAgent],
            ['devicetype'=>$deviceType, 'device' => $userAgent]
        );

        $allData = Device::get();
        $text = '<table>';
        foreach($allData as $ff){
            $data = $ff->device;
            $start = strpos($data, '(');
            $end = strpos($data, ')');
            $text .= '<tr>';
            $text .= '<td>'.substr($data, $start+1, $end-$start-1).'</td>';
            $text .= '<td>'.$data.'</td>';            
            $text .= '</tr>';
        }
        $text .= '</table>';
        echo $text;
    }
}
