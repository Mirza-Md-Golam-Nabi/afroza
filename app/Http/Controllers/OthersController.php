<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use App\Model\Stock;

class OthersController extends Controller
{
    public function upcomingPrice(){
        $title = "Upcoming Price";
        $priceList = DB::table('product_price as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', 'a.quantity', 'a.price', 'b.product_name')
                ->whereIn('a.status', [1, 0])
                ->orderBy('b.product_name', 'asc')
                ->orderBy('a.id', 'asc')
                ->get();

        $stockSummary = [];

        foreach ($priceList as $key => $value) {
            if(SessionController::filterByDow($stockSummary,$value->product_id)){
                $resultArr = SessionController::filterByDow($stockSummary,$value->product_id);

                $newarray = array_keys($resultArr);
                $insertArray = [
                    'price' => $value->price / $value->quantity,
                    'quantity' => $value->quantity
                ];
                $stockSummary[$newarray[0]]['upcoming'][count($stockSummary[$newarray[0]]['upcoming'])] = $insertArray;
            }else{
                $currentStock = Stock::select('applicable_stock')->where('product_id', $value->product_id)->first();
                $insertArray = [
                    'price' => $value->price / $value->quantity,
                    'quantity' => $currentStock->applicable_stock
                ];
                $newArray = [];
                $newArray['product_id']     = $value->product_id;
                $newArray['product_name']   = $value->product_name;
                $newArray['current']        = $insertArray;
                $newArray['upcoming']       = [];
                array_push($stockSummary, $newArray);
            }
        }

        $data = [
            'stockSummary' => $stockSummary,
        ];

        return view('admin.others.upcomingprice')->with(['title'=>$title, 'data'=>$data]);
    }
}
