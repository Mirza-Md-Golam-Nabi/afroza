<?php

namespace App\Http\Controllers;

use App\Model\Brand;
use App\Model\Stock;
use App\Model\Stockin;
use App\Model\Stockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function dateReport(){
        $title = "Daily Report";
        $url = "admin.report.date.details";
        $stockinData = Stockin::select('date')
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->take(20)
                    ->get();

        $stockoutData = Stockout::select('date')
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->take(30)
                    ->get();

        $data = [];
        foreach($stockinData as $value){
            array_push($data, [
                'date' => $value->date,
                'original' => $value->date,
            ]);
        }

        foreach ($stockoutData as $value) {
            // check the date already exist or not. if date is not exist, add a new array.
            if(empty(array_search($value->date, array_column($data, 'date')))){
                array_push($data, [
                    'date'      => $value->date,
                    'original'  => $value->date,
                ]);
            }
        }

        // sorting the date.
        usort($data, function ($object1, $object2) {
            return $object1['date'] < $object2['date'];
        });

        // change the date format from Y-m-d to d-m-y. e.g - 2021-12-30 to 30-12-21
        foreach($data as $key=>$dat){
            $data[$key]['date'] = date("d-m-y", strtotime($dat['date']));
        }

        $all_data = [
            'title' => $title,
            'url'   => $url,
            'data'  => $data,
            'model' => 'App\\\Model\\\Stockout',
        ];
        return view('admin.stock.stockDate')->with($all_data);
    }

    public function totalDailyProfit(Request $request){
        $month = $request->get('month');
        $year  = $request->get('year');

        $stockout = Stockout::select('date as full_date', DB::raw('DATE_FORMAT(date, "%d %b %Y") as date'), DB::raw('DATE_FORMAT(date, "%W") as day'), DB::raw('(SUM(selling_price) - SUM(buying_price)) as profit'))
                    ->where(DB::raw('MONTH(date)'), $month)
                    ->where(DB::raw('YEAR(date)'), $year)
                    ->orderBy('date', 'asc')
                    ->groupBy('date')
                    ->get();

        $allData = [
            'title' => 'Daily Profit',
            'month' => $month,
            'year' => $year,
            'profitData' => $stockout,
        ];

        return view('admin.report.profit.daily')->with($allData);
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
            array_push($stockSummary, [
                'product_id'   => $value->product_id,
                'product_name' => $value->product_name,
                'stockin'      => number_format($value->stockin),
                'stockout'     => 0,
                'profit'       => 0,
            ]);
        }

        foreach ($stockoutData as $key => $value) {
            if(SessionController::filterByDow($stockSummary,$value->product_id)){
                $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);
                $newarray = array_keys($resultArr);
                $stockSummary[$newarray[0]]['stockout'] = number_format($value->stockout);
                $stockSummary[$newarray[0]]['profit']   = number_format($value->sell - $value->buy);
                $profit += $value->sell - $value->buy;
            }else{
                array_push($stockSummary, [
                    'product_id'   => $value->product_id,
                    'product_name' => $value->product_name,
                    'stockin'      => 0,
                    'stockout'     => number_format($value->stockout),
                    'profit'       => number_format($value->sell - $value->buy),
                ]);
                $profit += $value->sell - $value->buy;
            }
        }

        usort($stockSummary, function ($object1, $object2) {
            return $object1['product_name'] > $object2['product_name'];
        });

        $all_data = [
            'title'         => $title,
            'date'          => $date,
            'stockSummary'  => $stockSummary,
            'lastUpdate'    => $lastUpdate,
            'profit'        => $profit,
        ];

        return view('admin.report.stockDate')->with($all_data);
    }

    public function weeklyReport(){
        $title = "Weekly Report";
        $data = SessionController::weekly();

        return view('admin.report.stockWeek')->with(['title'=>$title, 'stockSummary'=>$data['stockSummary'], 'profit'=>$data['profit']]);
    }

    public function last3MonthReport(){
        $data = SessionController::last3Month();
        $data['title'] = "Last 3 Months Report";

        return view('admin.report.stockLast3Month')->with($data);
    }

    public function productList(){
        $title = "Product List";

        $productList = Stock::select('product_id', 'product_name')->where('status', 1)->orderBy('product_name', 'asc')->get();

        return view('admin.report.productList')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function monthlyReport($product_id){
        $product = Stock::select('product_id', 'product_name', 'quantity')->where('product_id', $product_id)->first();
        $stockSummary = [];
        for($i = 0; $i < 12; $i++){
            $month = SessionController::backwardMonthCalculation($i);
            $year = SessionController::backwardYearCalculation($i);

            $stockoutData = DB::table('stockout_history')
                        ->where([
                            ['product_id', '=', $product_id],
                            [DB::raw('MONTH(date)'), '=', $month],
                            [DB::raw('YEAR(date)'), '=', $year],
                        ])
                        ->sum('quantity');

            array_push($stockSummary, [
                'quantity' => $stockoutData,
                'month'    => SessionController::backwardMonthFullName($i),
            ]);
        }

        $data = [
            'title' => 'Monthly Report',
            'product'=> $product,
            'stockSummary'=> $stockSummary,
        ];

        return view('admin.report.stockMonth')->with($data);
    }

    public function monthlyProfit(Request $request){
        $title = 'Monthly Profit';
        $year = $request->get('year');
        $profitData = DB::table('stockout_history')
                ->select(DB::raw('(SUM(selling_price) - SUM(buying_price)) as profit'), DB::raw('MONTHNAME(date) as month'), DB::raw('MONTH(date) as month_id'))
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
        $year = $request->get('year') ?? date('Y');

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
