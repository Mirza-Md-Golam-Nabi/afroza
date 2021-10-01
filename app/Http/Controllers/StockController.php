<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Stock;
use App\Model\Stockin;
use App\Model\Stockout;
use Auth;
use DB;

class StockController extends Controller
{
    public function __construct(){
        $help = new HelperController;
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

    public function filterByDow($stockSummary, $dow){
        return array_filter($stockSummary, function($item) use ($dow) {
            if($item['date'] == $dow){
                return true;
            }
        });
    }

    public function stockCurrent(){
        $title = "Current Stock";
        $stockList = DB::table('stock')
                   ->select('product_id','product_name', 'quantity')
                   ->where('status', 1)
                   ->orderBy('product_name', 'asc')
                   ->get();

        return view('admin.report.current')->with(['title'=>$title,'stockList'=>$stockList]);
    }

    public function stockHistory($product_id){
        $title = "Stock History";
        $product = Stock::where('product_id', $product_id)->first();
        $stockinData = Stockin::select('date', DB::raw('SUM(quantity) AS stockin'))
                    ->where('product_id', $product_id)
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->skip(0)->take(10)
                    ->get();

        $stockoutData = Stockout::select('date', DB::raw('SUM(quantity) AS stockout'))
                    ->where('product_id', $product_id)
                    ->orderBy('date','desc')
                    ->groupBy('date')
                    ->skip(0)->take(50)
                    ->get();

        $inLast  = DB::table('stockin_history')->select('updated_at')->where('product_id', $product_id)->orderBy('id','desc')->first();
        $outLast = DB::table('stockout_history')->select('updated_at')->where('product_id', $product_id)->orderBy('id','desc')->first();
        
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
            $newArray['date']           = $value->date;
            $newArray['stockin']        = $value->stockin;
            $newArray['stockout']       = 0;
            array_push($stockSummary, $newArray);
        }

        foreach ($stockoutData as $key => $value) {
            if($this->filterByDow($stockSummary,$value->date)){
                $resultArr = $this->filterByDow($stockSummary,$value->date);
                $newarray = array_keys($resultArr);
                $stockSummary[$newarray[0]]['stockout'] = $value->stockout;
            }else{
                $newArray['date']           = $value->date;
                $newArray['stockin']        = 0;
                $newArray['stockout']       = $value->stockout;
                array_push($stockSummary, $newArray);
            } 
        }

        usort($stockSummary, function ($object1, $object2) { 
            return $object1['date'] < $object2['date']; 
        });

        return view('admin.report.stockProduct')->with(['title'=>$title, 'product'=>$product, 'stockSummary'=>$stockSummary, 'lastUpdate'=>$lastUpdate]);

    }
}
