<?php

namespace App\Http\Controllers;

use App\Model\Type;
use App\model\Stock;
use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\ProductPrice;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
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

    public function index(){
        $title = "Admin Dashboard";
        $profit = [];
        for($i = 0; $i < 3; $i++){
            $month = date("m",  strtotime( date( 'Y-m-01' )." -$i months"));
            $year = date("Y",  strtotime( date( 'Y-m-01' )." -$i months"));

            $data = DB::table('stockout_history as a')
                ->select(DB::raw('SUM(a.buying_price) AS buy'), DB::raw('SUM(a.selling_price) AS sell'))
                ->where(DB::raw('MONTH(a.date)'), $month)
                ->where(DB::raw('YEAR(a.date)'), $year)
                ->first();

            $profit[$i] = $data->sell - $data->buy;
        }

        $barChart = $this->barChart();
        $chartData = "";
        foreach($barChart as $value){
            $chartData .= "['".date('d-m', strtotime($value->date))."', ".$value->profit."],";
        }

        $barChartData = rtrim($chartData, ",");

        $data = [
            'chartData' => $barChartData
        ];

        $stock = new Stock;
        $stock_price = $stock->productPriceSum();

        $product = new ProductPrice;
        $product_price = $product->productPriceSum();

        $total_stock_price = $stock_price + $product_price;

        $all_data = [
            'title'  => $title,
            'profit' => $profit,
            'data'   => $data,
            'total_stock_price' => $total_stock_price,
        ];

        return view('admin.index')->with($all_data);
    }

    public function barChart(){
        $start_date = date('Y-m-d');
        $end_date   = date('Y-m-d', strtotime($start_date. '-30 days'));

        $data = DB::table('stockout_history')
        ->select('date', DB::raw('(SUM(selling_price) - SUM(buying_price)) as profit'))
        ->whereBetween('date', [$end_date, $start_date])
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

        return $data;
    }

}


/*
try{
    DB::beginTransaction();

    DB::commit();
}catch(Exception $e){
    DB::rollback();
}
*/
