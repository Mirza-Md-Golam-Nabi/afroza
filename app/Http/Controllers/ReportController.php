<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Brand;
use App\Model\Stock;
use App\Model\Stockin;
use App\Model\Stockout;
use DB;
use Auth;

class ReportController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if(isset(Auth::user()->group_id) AND Auth::user()->group_id != 1){
                Auth::logout();
                return redirect()->route('welcome');
            }elseif(!isset(Auth::user()->group_id)){
                return redirect()->route('welcome');
            }
            return $next($request);
        });
    }

    function searchInDate($search, $array) {
        foreach ($array as $key => $val) {
            if ($val['date'] === $search) {
                return 0;
            }
        }
        return 1;
    }

    public function dateReport(){
        $title = "Daily Report";
        $url = "admin.report.date.details";
        $stockinData = Stockin::select('date')
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->get();

        $stockoutData = Stockout::select('date')
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->get();

        $data = [];
        $newArray = [];
        foreach($stockinData as $key => $value){            
            $newArray['date'] = $value->date;
            array_push($data, $newArray);
        }

        foreach ($stockoutData as $key => $value) {            
            if($this->searchInDate($value->date, $data)){
                $newArray['date'] = $value->date;
                array_push($data, $newArray);
            }
        }
        
        usort($data, function ($object1, $object2) { 
            return $object1['date'] < $object2['date']; 
        });

        return view('admin.stock.stockDateArray')->with(['title'=>$title, 'url'=>$url, 'data'=>$data]);
    }

    public function dateDetailsReport($date){
        $title = "Stock History";

        $stockinData = DB::table('stockin_history as a')
                    ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                    ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockin'), 'b.product_name')
                    ->where('a.date', $date)
                    ->groupBy('a.product_id')
                    ->get();

        $stockoutData = DB::table('stockout_history as a')
                    ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                    ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), 'b.product_name')
                    ->where('a.date', $date)
                    ->groupBy('a.product_id')
                    ->get();

        $stockSummary = [];
        foreach($stockinData as $key => $value){
            $newArray = [];
            $newArray['product_id']     = $value->product_id;
            $newArray['product_name']   = $value->product_name;
            $newArray['stockin']        = $value->stockin;
            $newArray['stockout']       = 0;
            array_push($stockSummary, $newArray);
        }

        foreach ($stockoutData as $key => $value) {
            if(SessionController::filterByDow($stockSummary,$value->product_id)){
                $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                $newarray = array_keys($resultArr);
                $stockSummary[$newarray[0]]['stockout'] = $value->stockout;
            }else{
                $newArray = [];
                $newArray['product_id']     = $value->product_id;
                $newArray['product_name']   = $value->product_name;
                $newArray['stockin']        = 0; 
                $newArray['stockout']       = $value->stockout;
                array_push($stockSummary, $newArray);
            } 
        }

        return view('admin.report.stockDate')->with(['title'=>$title, 'date'=>$date, 'stockSummary'=>$stockSummary]);
    }

    public function weeklyReport(){
        $title = "Weekly Report";
        $stockSummary = [];
        for($i = 0; $i < 4; $i++){
            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), 'b.product_name')
                        ->where(DB::raw('WEEKOFYEAR(date)'), '=', DB::raw('WEEKOFYEAR(NOW()) - '.$i))
                        ->groupBy('a.product_id')
                        ->get();

            foreach ($stockoutData as $key => $value) {
                if($this->filterByDow($stockSummary,$value->product_id)){
                    $resultArr = $this->filterByDow($stockSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0)
                        $stockSummary[$newarray[0]]['current'] = $value->stockout;
                    elseif($i == 1)
                        $stockSummary[$newarray[0]]['prev1'] = $value->stockout;
                    elseif($i == 2)
                        $stockSummary[$newarray[0]]['prev2'] = $value->stockout;
                    elseif($i == 3)
                        $stockSummary[$newarray[0]]['prev3'] = $value->stockout;
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
            }
        }

        return view('admin.report.stockWeek')->with(['title'=>$title, 'stockSummary'=>$stockSummary]);
    }

    public function last3MonthReport(){
        $title = "Last 3 Months Report";
        $currentMonth = date("m");
        $stockSummary = [];
        for($i = 0; $i < 3; $i++){
            $month = $currentMonth - $i;
            if($currentMonth >= $month){
                $year = date('Y');
            }else{
                $year = date('Y') - 1;
            }

            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), 'b.product_name')
                        ->where(DB::raw('MONTH(a.date)'), $month)
                        ->where(DB::raw('YEAR(a.date)'), $year)
                        ->groupBy('a.product_id')
                        ->get();

            foreach ($stockoutData as $key => $value) {
                if($this->filterByDow($stockSummary,$value->product_id)){
                    $resultArr = $this->filterByDow($stockSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0)
                        $stockSummary[$newarray[0]]['current_out'] = $value->stockout;
                    elseif($i == 1)
                        $stockSummary[$newarray[0]]['prev1_out'] = $value->stockout;
                    elseif($i == 2)
                        $stockSummary[$newarray[0]]['prev2_out'] = $value->stockout;
                }else{
                    $newArray = [];
                    $newArray['product_id']     = $value->product_id;
                    $newArray['product_name']   = $value->product_name;
                    $newArray['current_out']    = $i == 0 ? $value->stockout : 0;
                    $newArray['prev1_out']      = $i == 1 ? $value->stockout : 0;
                    $newArray['prev2_out']      = $i == 2 ? $value->stockout : 0;
                    array_push($stockSummary, $newArray);
                } 
            }
        }
        
        return view('admin.report.stockLast3Month')->with(['title'=>$title, 'stockSummary'=>$stockSummary]);
    }

    public function productList(){
        $title = "Product List";

        $productList = Stock::select('product_id', 'product_name')->where('status', 1)->orderBy('product_name', 'asc')->get();

        return view('admin.report.productList')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function monthlyReport($product_id){
        $title = "Monthly Report";
        $product = Stock::select('product_id', 'product_name', 'quantity')->where('product_id', $product_id)->first();
        $currentMonth = date("m");
        $stockSummary = [];
        for($i = 0; $i < 12; $i++){
            $month = $currentMonth - $i;
            if($currentMonth >= $month){
                $year = date('Y');
            }else{
                $year = date('Y') - 1;
            }

            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select(DB::raw('SUM(a.quantity) AS stockout'))
                        ->where('a.product_id', $product_id)
                        ->where(DB::raw('MONTH(a.date)'), $month)
                        ->where(DB::raw('YEAR(a.date)'), $year)
                        ->groupBy('a.product_id')
                        ->get();

            $newArray = [];
            $newArray['quantity'] = count($stockoutData) > 0 ? $stockoutData[0]->stockout : 0;
            $newArray['month']    = date("F", strtotime('-'.$i.' month'));
            array_push($stockSummary, $newArray);
        }
        
        return view('admin.report.stockMonth')->with(['title'=>$title, 'product'=>$product, 'stockSummary'=>$stockSummary]);
    }

    public function yearlyReport(){
        $title = "Yearly Report";
        $currentYear = date("Y");
        $stockSummary = [];
        for($i = 0; $i < 3; $i++){
            $year = $currentYear - $i;

            $stockoutData = DB::table('stockout_history as a')
                        ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                        ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), 'b.product_name')
                        ->where(DB::raw('YEAR(a.date)'), $year)
                        ->groupBy('a.product_id')
                        ->get();

            foreach ($stockoutData as $key => $value) {
                if($this->filterByDow($stockSummary,$value->product_id)){
                    $resultArr = $this->filterByDow($stockSummary,$value->product_id);
                    $newarray = array_keys($resultArr);
                    if($i == 0)
                        $stockSummary[$newarray[0]]['current_out'] = $value->stockout;
                    elseif($i == 1)
                        $stockSummary[$newarray[0]]['prev1_out'] = $value->stockout;
                    elseif($i == 2)
                        $stockSummary[$newarray[0]]['prev2_out'] = $value->stockout;
                }else{
                    $newArray = [];
                    $newArray['product_id']     = $value->product_id;
                    $newArray['product_name']   = $value->product_name;
                    $newArray['current_out']    = $i == 0 ? $value->stockout : 0;
                    $newArray['prev1_out']      = $i == 1 ? $value->stockout : 0;
                    $newArray['prev2_out']      = $i == 2 ? $value->stockout : 0;
                    array_push($stockSummary, $newArray);
                } 
            }
        }
        
        return view('admin.report.stockYear')->with(['title'=>$title, 'stockSummary'=>$stockSummary]);
    }

    public function companyReport(Request $request){        
        $serial_month = $request->get('serial');
        $company_name = $request->get('name');
        $has_year = $request->get('year');
        $year = $request->get('year') ? $request->get('year') : $year = date('Y');

        $brand = Brand::select('id', 'brand_name')->where('brand_name', $company_name)->first();
        $title = $brand->brand_name." Report";

        $serial = 0;
        if($serial_month){
            $serial = 1;
        }

        $request_data = [
            'brand' => $brand->id,
            'serial' => $serial,
            'year' => $year
        ];

        $monthList = SessionController::monthList($serial);
        $stockSummary = SessionController::dataFetch($request_data);

        if($has_year){
            return SessionController::ajaxData($monthList, $stockSummary);
        }

        return view('admin.report.company')->with(['title'=>$title, 'monthList'=>$monthList, 'stockSummary'=>$stockSummary, 'brand'=>$brand->brand_name]);
    }



}


