<?php

namespace App\Http\Controllers;

use DB;

use App\Model\Brand;
use App\Model\Stock;
use App\Model\Stockin;
use App\Model\Stockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    ->orderBy('date', 'desc')
                    ->skip(0)
                    ->take(20)
                    ->get();

        $stockoutData = Stockout::select('date')
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->skip(0)
                    ->take(30)
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
        $profit = 0;

        $stockinData = DB::table('stockin_history as a')
                    ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                    ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockin'), 'b.product_name')
                    ->where('a.date', $date)
                    ->groupBy('a.product_id')
                    ->get();

        $stockoutData = DB::table('stockout_history as a')
                    ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                    ->select('a.product_id', DB::raw('SUM(a.quantity) AS stockout'), DB::raw('SUM(a.buying_price) AS buy'), DB::raw('SUM(a.selling_price) AS sell'), 'b.product_name')
                    ->where('a.date', $date)
                    ->groupBy('a.product_id')
                    ->get();

        $inLast  = DB::table('stockin_history')->select('updated_at')->where('date', $date)->orderBy('id','desc')->first();
        $outLast = DB::table('stockout_history')->select('updated_at')->where('date', $date)->orderBy('id','desc')->first();

        if($inLast && $outLast){
            $lastUpdate = $inLast->updated_at > $outLast->updated_at ? $inLast->updated_at : $outLast->updated_at;
        }elseif($inLast){
            $lastUpdate = $inLast->updated_at;
        }elseif($outLast){
            $lastUpdate = $outLast->updated_at;
        }else{
            $lastUpdate = NULL;
        }

        $stockSummary = [];
        foreach($stockinData as $key => $value){
            $newArray = [];
            $newArray['product_id']     = $value->product_id;
            $newArray['product_name']   = $value->product_name;
            $newArray['stockin']        = $value->stockin;
            $newArray['stockout']       = 0;
            $newArray['profit']         = 0;
            array_push($stockSummary, $newArray);
        }

        foreach ($stockoutData as $key => $value) {
            if(SessionController::filterByDow($stockSummary,$value->product_id)){
                $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                $newarray = array_keys($resultArr);
                $stockSummary[$newarray[0]]['stockout'] = $value->stockout;
                $stockSummary[$newarray[0]]['profit']   = $value->sell - $value->buy;
                $profit += $value->sell - $value->buy;
            }else{
                $newArray = [];
                $newArray['product_id']     = $value->product_id;
                $newArray['product_name']   = $value->product_name;
                $newArray['stockin']        = 0;
                $newArray['stockout']       = $value->stockout;
                $newArray['profit']         = $value->sell - $value->buy;
                array_push($stockSummary, $newArray);
                $profit += $value->sell - $value->buy;
            }
        }

        usort($stockSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        return view('admin.report.stockDate')->with(['title'=>$title, 'date'=>$date, 'stockSummary'=>$stockSummary, 'lastUpdate'=>$lastUpdate, 'profit'=>$profit]);
    }

    public function weeklyReport(){
        $title = "Weekly Report";
        $data = SessionController::weekly();

        return view('admin.report.stockWeek')->with(['title'=>$title, 'stockSummary'=>$data['stockSummary'], 'profit'=>$data['profit']]);
    }

    public function last3MonthReport(){
        $title = "Last 3 Months Report";

        $data = SessionController::last3Month();

        return view('admin.report.stockLast3Month')->with(['title'=>$title, 'stockSummary'=>$data['stockSummary'], 'profit'=>$data['profit']]);
    }

    public function productList(){
        $title = "Product List";

        $productList = Stock::select('product_id', 'product_name')->where('status', 1)->orderBy('product_name', 'asc')->get();

        return view('admin.report.productList')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function monthlyReport($product_id){
        $title = "Monthly Report";
        $product = Stock::select('product_id', 'product_name', 'quantity')->where('product_id', $product_id)->first();
        $stockSummary = [];
        for($i = 0; $i < 12; $i++){
            $month = SessionController::monthCalculation($i);
            $year = SessionController::yearCalculation($i);

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
            $newArray['month']    = date("F",  strtotime( date( 'Y-m-01' )." -$i months"));
            array_push($stockSummary, $newArray);
        }

        return view('admin.report.stockMonth')->with(['title'=>$title, 'product'=>$product, 'stockSummary'=>$stockSummary]);
    }

    public function monthlyProfit(Request $request){
        $title = 'Monthly Profit';
        $year = $request->get('year');
        $profitData = DB::table('stockout_history')
                ->select(DB::raw('(SUM(selling_price) - SUM(buying_price)) as profit'), DB::raw('MONTHNAME(date) as month'))
                ->where(DB::raw('YEAR(date)'), $year)
                ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
                ->get();

        return view('admin.report.profit.monthly')->with(['title'=>$title, 'year'=>$year, 'profitData'=>$profitData]);
    }

    public function yearlyReport(){
        $title = "Yearly Report";

        $data = SessionController::yearly();

        return view('admin.report.stockYear')->with(['title'=>$title, 'stockSummary'=>$data['stockSummary'], 'profit'=>$data['profit']]);
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
        $data = SessionController::dataFetch($request_data);

        if($has_year){
            return SessionController::ajaxData($monthList, $data);
        }

        return view('admin.report.company')->with(['title'=>$title, 'monthList'=>$monthList, 'stockSummary'=>$data['stock'], 'totalStock'=>$data['totalStock'], 'brand'=>$brand->brand_name]);
    }

    public function ajaxReport(Request $request){
        /** name means 1=weekly / 2=monthly / 3=yearly */
        $name = $request->get("name");
        /** data means 1=stock / 2=profit */
        $data = $request->get("data");
        $serial = $request->get("serial");
        $year = $request->get("year");
        if($year){
            $brand = Brand::select('id', 'brand_name')->where('brand_name', $name)->first();
            $request_data = [
                'brand' => $brand->id,
                'serial' => $serial,
                'year' => $year,
                'data' => $data
            ];
            if($data == 1){
                return ViewController::companyStock($request_data);
            }else{
                return ViewController::companyProfit($request_data);
            }
        }else{
            if($name == 1){
                if($data == 1){
                    return ViewController::weeklyStock();
                }else{
                    return ViewController::weeklyProfit();
                }
            }elseif($name == 2){
                if($data == 1){
                    return ViewController::last3MonthStock();
                }else{
                    return ViewController::last3MonthProfit();
                }
            }elseif($name == 3){
                if($data == 1){
                    return ViewController::yearlyStock();
                }else{
                    return ViewController::yearlyProfit();
                }
            }else{
                return "";
            }
        }
    }

}


