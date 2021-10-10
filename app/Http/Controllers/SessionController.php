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
              ->skip(0)
              ->take(30)
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

        usort($stockSummary, function ($object1, $object2) { 
            return $object1['product_name'] > $object2['product_name']; 
        });

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

    public static function weekly(){
        $stockSummary = [];
        $profitSummary = [];
        $profit = [];
        for($i = 0; $i < 4; $i++){
            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), DB::raw('SUM(a.buying_price) AS buy'), DB::raw('SUM(a.selling_price) AS sell'), 'b.product_name')
                        ->where(DB::raw('WEEKOFYEAR(date)'), '=', DB::raw('WEEKOFYEAR(NOW()) - '.$i))
                        ->groupBy('a.product_id')
                        ->get();

            $profit[$i] = 0;
            foreach ($stockoutData as $key => $value) {
                $profit[$i] += ($value->sell - $value->buy);
                if(SessionController::filterByDow($stockSummary,$value->product_id)){
                    $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0){
                        $stockSummary[$newarray[0]]['current'] = $value->stockout;
                    }elseif($i == 1){
                        $stockSummary[$newarray[0]]['prev1'] = $value->stockout;
                    }elseif($i == 2){
                        $stockSummary[$newarray[0]]['prev2'] = $value->stockout;
                    }elseif($i == 3){
                        $stockSummary[$newarray[0]]['prev3'] = $value->stockout;
                    }
                }else{
                    $newArray = [];
                    $newArray['product_id']     = $value->product_id;
                    $newArray['product_name']   = $value->product_name;
                    $newArray['current']        = $i == 0 ? $value->stockout : 0;
                    $newArray['prev1']          = $i == 1 ? $value->stockout : 0;
                    $newArray['prev2']          = $i == 2 ? $value->stockout : 0;
                    $newArray['prev3']          = $i == 3 ? $value->stockout : 0;
                    array_push($stockSummary, $newArray);
                }

                if(SessionController::filterByDow($profitSummary,$value->product_id)){
                    $resultArr = SessionController::filterByDow($profitSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0){
                        $profitSummary[$newarray[0]]['profit0'] = $value->sell - $value->buy;
                    }elseif($i == 1){
                        $profitSummary[$newarray[0]]['profit1'] = $value->sell - $value->buy;
                    }elseif($i == 2){
                        $profitSummary[$newarray[0]]['profit2'] = $value->sell - $value->buy;
                    }elseif($i == 3){
                        $profitSummary[$newarray[0]]['profit3'] = $value->sell - $value->buy;
                    }
                }else{
                    $newArray = [];
                    $newArray['product_id']   = $value->product_id;
                    $newArray['product_name'] = $value->product_name;
                    $newArray['profit0']      = $i == 0 ? ($value->sell - $value->buy) : 0;
                    $newArray['profit1']      = $i == 1 ? ($value->sell - $value->buy) : 0;
                    $newArray['profit2']      = $i == 2 ? ($value->sell - $value->buy) : 0;
                    $newArray['profit3']      = $i == 3 ? ($value->sell - $value->buy) : 0;
                    array_push($profitSummary, $newArray);
                }
            }
        }

        usort($stockSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        usort($profitSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        $data = [
            'stockSummary' => $stockSummary,
            'profitSummary' => $profitSummary,
            'profit' => $profit
        ];

        return $data;
    }

    public static function last3Month(){
        $currentMonth = date("m");
        $stockSummary = [];
        $profitSummary = [];
        $profit = [];
        for($i = 0; $i < 3; $i++){
            $month = $currentMonth - $i;
            if($currentMonth >= $month){
                $year = date('Y');
            }else{
                $year = date('Y') - 1;
            }

            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), DB::raw('SUM(a.buying_price) AS buy'), DB::raw('SUM(a.selling_price) AS sell'), 'b.product_name')
                        ->where(DB::raw('MONTH(a.date)'), $month)
                        ->where(DB::raw('YEAR(a.date)'), $year)
                        ->groupBy('a.product_id')
                        ->get();

            $profit[$i] = 0;
            foreach ($stockoutData as $key => $value) {
                $profit[$i] += ($value->sell - $value->buy);
                if(SessionController::filterByDow($stockSummary,$value->product_id)){
                    $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0){
                        $stockSummary[$newarray[0]]['current_out'] = $value->stockout;
                    }elseif($i == 1){
                        $stockSummary[$newarray[0]]['prev1_out'] = $value->stockout;
                    }elseif($i == 2){
                        $stockSummary[$newarray[0]]['prev2_out'] = $value->stockout;
                    }
                }else{
                    $newArray = [];
                    $newArray['product_id']     = $value->product_id;
                    $newArray['product_name']   = $value->product_name;
                    $newArray['current_out']    = $i == 0 ? $value->stockout : 0;
                    $newArray['prev1_out']      = $i == 1 ? $value->stockout : 0;
                    $newArray['prev2_out']      = $i == 2 ? $value->stockout : 0;
                    array_push($stockSummary, $newArray);
                }

                if(SessionController::filterByDow($profitSummary,$value->product_id)){
                    $resultArr = SessionController::filterByDow($profitSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0){
                        $profitSummary[$newarray[0]]['profit0'] = $value->sell - $value->buy;
                    }elseif($i == 1){
                        $profitSummary[$newarray[0]]['profit1'] = $value->sell - $value->buy;
                    }elseif($i == 2){
                        $profitSummary[$newarray[0]]['profit2'] = $value->sell - $value->buy;
                    }
                }else{
                    $newArray = [];
                    $newArray['product_id']     = $value->product_id;
                    $newArray['product_name']   = $value->product_name;
                    $newArray['profit0']        = $i == 0 ? ($value->sell - $value->buy) : 0;
                    $newArray['profit1']        = $i == 1 ? ($value->sell - $value->buy) : 0;
                    $newArray['profit2']        = $i == 2 ? ($value->sell - $value->buy) : 0;
                    array_push($profitSummary, $newArray);
                }
            }
        }

        usort($stockSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        usort($profitSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        $data = [
            'stockSummary' => $stockSummary,
            'profitSummary' => $profitSummary,
            'profit' => $profit
        ];

        return $data;
    }

    public static function yearly(){
        $currentYear = date("Y");
        $stockSummary = [];
        $profitSummary = [];
        $profit = [];
        for($i = 0; $i < 3; $i++){
            $year = $currentYear - $i;

            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), DB::raw('SUM(a.buying_price) AS buy'), DB::raw('SUM(a.selling_price) AS sell'), 'b.product_name')
                        ->where(DB::raw('YEAR(a.date)'), $year)
                        ->groupBy('a.product_id')
                        ->get();

            $profit[$i] = 0;
            foreach ($stockoutData as $key => $value) {
                $profit[$i] += ($value->sell - $value->buy);
                if(SessionController::filterByDow($stockSummary,$value->product_id)){
                    $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0){
                        $stockSummary[$newarray[0]]['current_out'] = $value->stockout;
                    }elseif($i == 1){
                        $stockSummary[$newarray[0]]['prev1_out'] = $value->stockout;
                    }elseif($i == 2){
                        $stockSummary[$newarray[0]]['prev2_out'] = $value->stockout;
                    }
                }else{
                    $newArray = [];
                    $newArray['product_id']     = $value->product_id;
                    $newArray['product_name']   = $value->product_name;
                    $newArray['current_out']    = $i == 0 ? $value->stockout : 0;
                    $newArray['prev1_out']      = $i == 1 ? $value->stockout : 0;
                    $newArray['prev2_out']      = $i == 2 ? $value->stockout : 0;
                    array_push($stockSummary, $newArray);
                }

                if(SessionController::filterByDow($profitSummary,$value->product_id)){
                    $resultArr = SessionController::filterByDow($profitSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0){
                        $profitSummary[$newarray[0]]['profit0'] = $value->sell - $value->buy;
                    }elseif($i == 1){
                        $profitSummary[$newarray[0]]['profit1'] = $value->sell - $value->buy;
                    }elseif($i == 2){
                        $profitSummary[$newarray[0]]['profit2'] = $value->sell - $value->buy;
                    }
                }else{
                    $proft = [];
                    $proft['product_id']    = $value->product_id;
                    $proft['product_name']  = $value->product_name;
                    $proft['profit0']       = $i == 0 ? ($value->sell - $value->buy) : 0;
                    $proft['profit1']       = $i == 1 ? ($value->sell - $value->buy) : 0;
                    $proft['profit2']       = $i == 2 ? ($value->sell - $value->buy) : 0;
                    array_push($profitSummary, $proft);
                }
            }
        }

        usort($stockSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        usort($profitSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        $data = [
            'stockSummary' => $stockSummary,
            'profitSummary' => $profitSummary,
            'profit' => $profit
        ];

        return $data;
    }
}
