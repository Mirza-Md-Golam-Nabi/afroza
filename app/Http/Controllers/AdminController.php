<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Type;
use App\Model\Category;
use Auth;
use DB;

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
        $currentMonth = date("m");
        $profit = [];
        for($i = 0; $i < 3; $i++){
            $month = $currentMonth - $i;
            $year = ($currentMonth >= $month) ? date('Y') : date('Y') - 1;

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

        return view('admin.index')->with(['title'=>$title, 'profit'=>$profit, 'data'=>$data]);
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