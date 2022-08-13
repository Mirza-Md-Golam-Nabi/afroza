<?php

namespace App\Http\Controllers;

use Exception;
use App\Model\Stock;
use App\Model\Stockin;
use App\Model\Stockout;
use App\Model\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    private $helper;
    public function __construct(){
        $this->helper = new HelperController;
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
        $stock = new Stock();
        $stockList = $stock->currentAll();

        $all_data = [
            'title'     => $title,
            'stockList' => $stockList,
        ];

        return view('admin.report.current')->with($all_data);
    }

    public function stockHistory($product_id){
        $title = "Stock History";

        $stock = new Stock();
        $product = $stock->currentProduct($product_id);

        $stockin = new Stockin();
        $stockinData = $stockin->singleProduct($product_id);

        $stockout = new Stockout();
        $stockoutData = $stockout->singleProduct($product_id);

        $inLast  = $stockin->lastUpdateTimeForProduct($product_id);
        $outLast = $stockout->lastUpdateTimeForProduct($product_id);

        $lastUpdate = $this->helper->lastUpdate($inLast, $outLast);

        $stockSummary = [];
        foreach($stockinData as $key => $value){
            array_push($stockSummary, [
                'date'      => $value->date,
                'stockin'   => number_format($value->stockin),
                'stockout'  => 0,
                'profit'    => 0,
            ]);
        }

        foreach ($stockoutData as $key => $value) {
            if($this->filterByDow($stockSummary,$value->date)){
                $resultArr = $this->filterByDow($stockSummary,$value->date);
                $newarray = array_keys($resultArr);
                $stockSummary[$newarray[0]]['stockout'] = number_format($value->stockout);
                $stockSummary[$newarray[0]]['profit']   = number_format(($value->sell - $value->buy), 1);
            }else{
                array_push($stockSummary, [
                    'date'     => $value->date,
                    'stockin'  => 0,
                    'stockout' => number_format($value->stockout),
                    'profit'   => number_format(($value->sell - $value->buy), 1),
                ]);
            }
        }

        // date sorting from new to old
        $dateSorting = $this->helper->dateSorting($stockSummary);

        // date format : d-m-y i.e 30-12-22
        $stockSummary = $this->helper->dateFormatting($dateSorting);

        $all_data = [
            'title'         => $title,
            'product'       => $product,
            'stockSummary'  => $stockSummary,
            'lastUpdate'    => $lastUpdate,
        ];

        return view('admin.report.stockProduct')->with($all_data);

    }

    public function add(){
        $title = "Stock Price Add";
        $productList = DB::table('products as a')
                    ->leftJoin('stock as b', 'b.product_id', '=', 'a.id')
                    ->select('a.id', 'a.product_name')
                    ->where('a.status', 1)
                    ->where('b.applicable_stock', 0)
                    ->where('b.quantity', '>', 0)
                    ->orderBy('a.product_name', 'asc')
                    ->get();
        return view('admin.stock.priceadd')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function store(Request $request){
        $product_id = $request->product_id;
        $quantity   = $request->quantity;
        $price      = $request->price;

        $allData = [];
        for($i = 0; $i < count($product_id); $i++){
            array_push($allData, [
                'product_id' => $product_id[$i],
                'quantity'   => $quantity[$i],
                'price'      => $price[$i],
            ]);
        }

        try{
            DB::beginTransaction();

            foreach($allData as $data){
                $productPrice = ProductPrice::where('product_id', $data['product_id'])->where('status', 1)->first();
                if($productPrice){
                    $store = new ProductPrice;
                    $store->product_id  = $data['product_id'];
                    $store->quantity    = $data['quantity'];
                    $store->price       = $data['quantity'] * $data['price'];
                    $store->status      = 0;
                    $store->save();
                }else{
                    $store = new ProductPrice;
                    $store->product_id  = $data['product_id'];
                    $store->quantity    = $data['quantity'];
                    $store->price       = $data['quantity'] * $data['price'];
                    $store->status      = 1;
                    $store->save();

                    DB::table('stock')->where('product_id', $data['product_id'])->update(['current_price'=>$data['price'], 'applicable_stock'=>$data['quantity']]);
                }
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        session()->flash('success', 'Price Added Successfully.');
        return redirect()->back();
    }
}
