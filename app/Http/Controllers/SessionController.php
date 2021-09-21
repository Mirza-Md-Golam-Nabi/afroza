<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Model\Brand;
use App\Model\Category;
use App\Model\Product;
use App\Model\Type;
use App\User;
use Auth;
use DB;

class SessionController extends Controller
{
    public static function stockDate($table_name){
        return DB::table($table_name)
              ->select('date')
              ->groupBy('date')
              ->orderBy('date', 'desc')
              ->get();
    }

    public static function brandList(){
        return Brand::select('brand_name')
            ->orderBy('brand_name')
            ->get();
    }

    /**
     * 1 = serial Month i.e Jan to Dec
     * 0 = Last 12 month Backward  
    */

    public static function monthList($data){
        if($data == 1){
            return self::serialMonth();
        }else{
            return self::backwardMonth();
        }
    }

    public static function serialMonth(){
        $monthName = [];
        for($i = 1; $i <= 12; $i++){
            array_push($monthName, date("M", mktime(0, 0, 0, $i)));
        }
        return $monthName;
    }

    public static function backwardMonth(){
        $monthName = [];
        for($i = 0; $i < 12; $i++){
            array_push($monthName, date("M", strtotime("-".$i." month")));
        }
        return $monthName;
    }

    public static function dataFetch($data){
        if($data['serial'] == 1){
            return self::serialMonthData($data['brand'], $data['year']);
        }else{
            return self::backwardMonthData($data['brand']);
        }
    }

    public static function filterByDow($stockSummary, $dow){
        return array_filter($stockSummary, function($item) use ($dow) {
            if($item['product_id'] == $dow){
                return true;
            }
        });
    }

    public static function serialMonthData($brand_id, $year){
        $stockSummary = [];
        for($i = 1; $i <= 12; $i++){
            $stockSummary = self::dataFind($stockSummary, $brand_id, $month = $i, $year, ($i+1));
        }
        return $stockSummary;
    }

    public static function backwardMonthData($brand_id){
        $currentMonth = date("m");
        $stockSummary = [];
        for($i = 0; $i < 12; $i++){
            $month = $currentMonth - $i;
            $year = $currentMonth >= $month ? date('Y') : date('Y') - 1;

            $stockSummary = self::dataFind($stockSummary, $brand_id, $month, $year, ($i+1));            
        }

        return $stockSummary;
    }

    public static function dataFind($stockSummary, $brand_id, $month, $year, $i){
        $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', 'b.product_name', DB::raw('SUM(a.quantity) AS stockout'))
                        ->where('b.brand_id', $brand_id)
                        ->where(DB::raw('MONTH(a.date)'), $month)
                        ->where(DB::raw('YEAR(a.date)'), $year)
                        ->groupBy('a.product_id')
                        ->get();
                        
        foreach ($stockoutData as $key => $value) {
            if(SessionController::filterByDow($stockSummary,$value->product_id)){
                $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                $newarray = array_keys($resultArr);
                $stockSummary[$newarray[0]][$i] = $value->stockout;
            }else{
                $newArray = [];
                $newArray['product_id']    = $value->product_id;
                $newArray['product_name']  = $value->product_name;
                $newArray['1']        = $i == 1 ? $value->stockout : 0;
                $newArray['2']        = $i == 2 ? $value->stockout : 0;
                $newArray['3']        = $i == 3 ? $value->stockout : 0;
                $newArray['4']        = $i == 4 ? $value->stockout : 0;
                $newArray['5']        = $i == 5 ? $value->stockout : 0;
                $newArray['6']        = $i == 6 ? $value->stockout : 0;
                $newArray['7']        = $i == 7 ? $value->stockout : 0;
                $newArray['8']        = $i == 8 ? $value->stockout : 0;
                $newArray['9']        = $i == 9 ? $value->stockout : 0;
                $newArray['10']       = $i == 10 ? $value->stockout : 0;
                $newArray['11']       = $i == 11 ? $value->stockout : 0;
                $newArray['12']       = $i == 12 ? $value->stockout : 0;
                array_push($stockSummary, $newArray);
            } 
        }

        return $stockSummary;
    }

    public static function ajaxData($monthList, $stockSummary){
        $output = '';
        $output .= '<table class="table table-striped table-sm">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>';
            $output .= "";
            for($i = 0; $i < 12; $i++){
                $output .= '<th scope="col" style="text-align: center;">'.$monthList[$i].'</th>';
            }
            $output .= '</tr>
        </thead>
        <tbody>';
          foreach($stockSummary AS $stock){
            $output .= '<tr>
                <td>'.$stock['product_name'].'</td>';
                for($i = 1; $i <= 12; $i++){
                    $output .= '<td style="text-align: center;">'.$stock[$i].'</td>';
                }
                $output .= '</tr>';
          }
          $output .= '</tbody>
        </table>';
        return $output;
    } 
}
