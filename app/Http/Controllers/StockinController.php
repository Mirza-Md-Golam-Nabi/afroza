<?php

namespace App\Http\Controllers;

use Exception;

use App\Model\Stock;
use App\Model\Product;
use App\Model\Stockin;
use App\Model\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockinController extends Controller
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

    public function stockinCreate(){
        $title = "Stock In";
        $product = new Product();
        $productList = $product->activeAll();

        $all_data = [
            'title'       => $title,
            'productList' => $productList,
        ];

        return view('admin.stock.stockin.create')->with($all_data);
    }

    public function stockinStore(Request $request){
        $this->validate($request, [
            'date' => 'required',
        ]);

        $invoice    = $request->invoice;
        $date       = $request->date;
        $product_id = $request->product_id;
        $quantity   = $request->quantity;
        $price      = $request->price;

        if(empty($invoice)){
            $invoice = "N/A";
        }

        $stockData = [];
        for($i = 0; $i < count($product_id); $i++){
            array_push($stockData, [
                'product_id' => $product_id[$i],
                'quantity'   => $quantity[$i],
                'price'      => $price[$i],
            ]);
        }

        try{
            DB::beginTransaction();

            foreach($stockData as $stock){
                $stockin = new Stockin;
                $stockin->invoice_id    = $invoice;
                $stockin->date          = $date;
                $stockin->product_id    = $stock['product_id'];
                $stockin->quantity      = $stock['quantity'];
                $stockin->buying_price  = $stock['price'];
                $stockin->updated_by    = Auth::user()->id;
                $stockin->save();

                $productPrice = new ProductPrice();

                $productPriceStatus = 1;
                if($productPrice->productHas($stock['product_id'])){
                    $productPriceStatus = 0;

                    $data = Stock::where('product_id', $stock['product_id'])->update(['quantity'=>DB::raw('quantity + '.$stock["quantity"]), 'updated_by'=>Auth::user()->id]);
                }else{
                    $data = Stock::where('product_id', $stock['product_id'])->update(['quantity'=>DB::raw('quantity + '.$stock["quantity"]), 'current_price'=>$stock['price'], 'applicable_stock'=>$stock['quantity'], 'updated_by'=>Auth::user()->id]);
                }

                $productPrice = new ProductPrice;
                $productPrice->date         = $date;
                $productPrice->product_id   = $stock['product_id'];
                $productPrice->quantity     = $stock['quantity'];
                $productPrice->price        = $stock['quantity'] * $stock['price'];
                $productPrice->status       = $productPriceStatus;
                $productPrice->save();
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($data){
            session()->flash('success','Stock Added Successfully.');
            return redirect()->route('admin.stockin.create');
        }else{
            session()->flash('error','Stock does not Added successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function stockinList($date){
        $title = "Stock-in History by Group";
        $stockin = new Stockin();
        $dataList = DB::table('stockin_history as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', DB::raw('SUM(a.quantity) as quantity'), DB::raw('SUM(a.buying_price) as price'), 'b.product_name')
                ->where('a.date', $date)
                ->groupBy('a.date', 'a.product_id')
                ->orderBy('b.product_name', 'asc')
                ->get();

        $inLast  = $stockin->updateTimeForAll($date);
        $lastUpdate = $inLast->updated_at;

        $all_data = [
            'title'      => $title,
            'dataList'   => $dataList,
            'date'       => $date,
            'lastUpdate' => $lastUpdate,
        ];

        return view('admin.stock.stockin.list')->with($all_data);
    }

    public function stockinListAll($date){
        $title = "Stock-in History by All";
        $stockin = new Stockin();
        $dataList = DB::table('stockin_history as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', 'a.quantity', 'a.buying_price as price', 'b.product_name')
                ->where('a.date', $date)
                ->orderBy('b.product_name', 'asc')
                ->get();

        $inLast  = $stockin->updateTimeForAll($date);
        $lastUpdate = $inLast->updated_at;

        $all_data = [
            'title'      => $title,
            'dataList'   => $dataList,
            'date'       => $date,
            'lastUpdate' => $lastUpdate,
        ];

        return view('admin.stock.stockin.list')->with($all_data);
    }

    public function stockinEdit($date, $product_id){
        $title = "Stock In Edit";
        $stockinList = Stockin::where('date', $date)->where('product_id', $product_id)->get();

        $product = new Product();
        $productList = $product->activeAll();

        $all_data = [
            'title'         => $title,
            'stockinList'   => $stockinList,
            'productList'   => $productList,
            'date'          => $date,
            'productId'     => $product_id,
        ];

        return view('admin.stock.stockin.edit')->with($all_data);
    }

    public function stockinUpdate(Request $request){
        $this->validate($request, [
            'oldProductId'  => 'required',
            'oldDate'       => 'required',
            'date'          => 'required',
        ]);

        $oldProductId = $request->oldProductId;
        $oldDate    = $request->oldDate;
        $invoice    = $request->invoice;
        $date       = $request->date;
        $product_id = $request->product_id;
        $quantity   = $request->quantity;
        $price      = $request->price;

        if(empty($invoice)){
            $invoice = "N/A";
        }

        $allProduct = [];
        if(isset($product_id) && count($product_id) > 0){
            for($i = 0; $i < count($product_id); $i++){
                array_push($allProduct, [
                    'productId'  => $product_id[$i],
                    'quantity'   => $quantity[$i],
                    'price'      => $price[$i],
                ]);
            }
        }else{
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->back()->withInput();
        }

        $stockIn = Stockin::select('quantity')->where('date', $oldDate)->where('product_id', $oldProductId)->get();
        $allTotal = 0;
        foreach($stockIn as $stock){
            $allTotal += $stock->quantity;
        }

        try{
            DB::beginTransaction();

            Stockin::where('date', $oldDate)->where('product_id', $oldProductId)->delete();
            ProductPrice::where('date', $oldDate)->where('product_id', $oldProductId)->delete();

            $stock = Stock::where('product_id', $oldProductId)->update(['quantity' => DB::raw('quantity - '.$allTotal)]);

            foreach($allProduct as $product){
                $productPriceCheck = DB::table('product_price')->where('product_id', $product['productId'])->where('status', 1)->first();

                $productPriceStatus = 1;
                if($productPriceCheck){
                    $productPriceStatus = 0;

                    Stock::where('product_id', $product['productId'])->update(['quantity' => DB::raw('quantity + '.$product['quantity']), 'updated_by'=>Auth::user()->id]);
                }else{
                    Stock::where('product_id', $product['productId'])->update(['quantity' => DB::raw('quantity + '.$product['quantity']), 'current_price'=>$product['price'], 'applicable_stock'=>$product['quantity'], 'updated_by'=>Auth::user()->id]);
                }

                $productPrice = new ProductPrice;
                $productPrice->date         = $date;
                $productPrice->product_id   = $product['productId'];
                $productPrice->quantity     = $product['quantity'];
                $productPrice->price        = $product['quantity'] * $product['price'];
                $productPrice->status       = $productPriceStatus;
                $productPrice->save();

                $stockin = new Stockin;
                $stockin->invoice_id    = $invoice;
                $stockin->date          = $date;
                $stockin->product_id    = $product['productId'];
                $stockin->quantity      = $product['quantity'];
                $stockin->buying_price  = $product['price'];
                $stockin->updated_by    = Auth::user()->id;
                $stockin->save();
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($stockin){
            session()->flash('success','Stock Updated Successfully.');
            return redirect()->route('admin.stockin.list.all', $date);
        }else{
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function stockinDate(){
        $title = "Stock In Date";
        $url = "admin.stockin.list.all";
        $data = SessionController::stockDate('stockin_history');

        $all_data = [
            'title' => $title,
            'url'   => $url,
            'data'  => $data,
            'model' => 'App\\\Model\\\Stockin',
        ];

        return view('admin.stock.stockDate')->with($all_data);
    }
}
