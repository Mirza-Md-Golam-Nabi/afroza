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
        for($i = 0; $i < 2; $i++){
            $month = $currentMonth - $i;
            $year = ($currentMonth >= $month) ? date('Y') : date('Y') - 1;

            $data = DB::table('stockout_history as a')
                ->select(DB::raw('SUM(a.buying_price) AS buy'), DB::raw('SUM(a.selling_price) AS sell'))
                ->where(DB::raw('MONTH(a.date)'), $month)
                ->where(DB::raw('YEAR(a.date)'), $year)
                ->first();

            $profit['profit'.$i] = $data->sell - $data->buy;
        }
        return view('admin.index')->with(['title'=>$title, 'profit'=>$profit]);
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