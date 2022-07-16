<?php

namespace App\Http\Controllers;

use App\Model\Stock;
use App\Model\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SessionController;

class OthersController extends Controller
{
    public function upcomingPrice(){
        $title = "Upcoming Product Price";
        $priceList = DB::table('product_price as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', 'a.quantity', 'a.price', 'a.sell_price', 'b.product_name')
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
                    'quantity' => $currentStock->applicable_stock,
                    'profit' => $value->sell_price - $value->price,
                ];
                array_push($stockSummary, [
                    'product_id'     => $value->product_id,
                    'product_name'   => $value->product_name,
                    'current'        => $insertArray,
                    'upcoming'       => [],
                ]);
            }
        }

        $data = [
            'stockSummary' => $stockSummary,
        ];

        return view('admin.others.upcomingprice')->with(['title'=>$title, 'data'=>$data]);
    }

    public function previousPrice(){
        $title = "Previous Product Price";
        $productList = DB::table('products')
                    ->orderBy('product_name', 'asc')
                    ->orderBy('id', 'asc')
                    ->pluck('product_name','id');

        $previousPriceData = [];
        $array = [];
        foreach($productList as $product_id => $product_name){
            $productPriceList = ProductPrice::select('price', 'quantity')
                            ->where('product_id', $product_id)
                            ->where('status', 2)
                            ->orderBy('id', 'desc')
                            ->take(4)
                            ->get();

            $array['product_id'] = $product_id;
            $array['product_name'] = $product_name;
            $key_value = -1;
            if($productPriceList){
                foreach($productPriceList as $key => $price){
                    $array['price'][$key] = $price->price / $price->quantity;
                    $key_value = $key;
                }
                for($i = $key_value+1; $i < 4; $i++){
                    $array['price'][$i] = 0;
                }
            }
            array_push($previousPriceData, $array);
            $array = null;
        }

        return view('admin.others.previousprice')->with(['title'=>$title, 'previousPriceData'=>$previousPriceData]);
    }

    public function previousPriceId($product_id){
        $title = "Previous Product Price";
        $priceList = DB::table('product_price')
                ->select('price', 'quantity',
                    DB::raw('DATE_FORMAT(date, "%d-%m-%y") as date'),
                    DB::raw('(price / quantity) as per_bag'),
                    DB::raw('(sell_price - price) as profit')
                )
                ->where('product_id', $product_id)
                ->where('status', 2)
                ->orderBy('id', 'desc')
                ->get();

        return view('admin.others.product')->with(['title'=>$title, 'priceList'=>$priceList]);
    }
}
