<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\HelperController;
use App\Model\Product;
use App\Model\Stock;
use App\Model\Stockout;
use Auth;
use DB;

class StockoutController extends Controller
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
     
    public function stockoutCreate(){
        $title = "Stock Out";
        $productList = Product::where('status', 1)->orderBy('product_name', 'asc')->get();
        return view('admin.stock.stockout.create')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function stockoutStore(Request $request){
        $this->validate($request, [
            'date' => 'required',
        ]);

        $date       = $request->date;
        $product_id = $request->product_id;
        $quantity   = $request->quantity;

        if(empty($invoice)){
            $invoice = "N/A";
        }

        $stockData = [];
        for($i = 0; $i < count($product_id); $i++){
            $newArray = [];
            $newArray['product_id'] = $product_id[$i];
            $newArray['quantity'] = $quantity[$i];
            array_push($stockData, $newArray);
        }

        try{
            DB::beginTransaction();

            foreach($stockData as $stock){
                $stockin = new Stockout;
                $stockin->date          = $date;
                $stockin->product_id    = $stock['product_id'];
                $stockin->quantity      = $stock['quantity'];
                $stockin->updated_by    = Auth::user()->id;
                $stockin->save();

                $data = Stock::where('product_id', $stock['product_id'])->update(['quantity'=>DB::raw('quantity - '.$stock["quantity"])]);
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($data){
            session()->flash('success','Stock out Successfully.');
            return redirect()->route('admin.stockout.create');
        }else{
            session()->flash('error','Stock does not out successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function stockoutList($date){
        $title = "Stock-out History";
        $dataList = DB::table('stockout_history as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', DB::raw('SUM(a.quantity) as quantity'), 'b.product_name')
                ->where('a.date', $date)
                ->groupBy('a.date', 'a.product_id')
                ->orderBy('a.date', 'desc')
                ->get();

        $outLast = DB::table('stockout_history')->select('updated_at')->where('date', $date)->orderBy('id','desc')->first();
        $lastUpdate = $outLast->updated_at;

        return view('admin.stock.stockout.list')->with(['title'=>$title,'dataList'=>$dataList, 'date'=>$date, 'lastUpdate'=>$lastUpdate]);
    }

    public function stockoutEdit($date, $product_id){
        $title = "Stock Out Edit";
        $stockoutList = Stockout::where('date', $date)->where('product_id', $product_id)->get();
        $productList = Product::where('status', 1)->orderBy('product_name', 'asc')->get();
        
        return view('admin.stock.stockout.edit')->with(['title'=>$title, 'stockoutList'=>$stockoutList,'productList'=>$productList, 'date'=>$date, 'product_id'=>$product_id]);
    }

    public function stockoutUpdate(Request $request){
        $this->validate($request, [
            'oldProduct_id' => 'required',
            'oldDate'       => 'required',
            'date'          => 'required',
        ]);

        $oldProduct_id = $request->oldProduct_id;
        $oldDate       = $request->oldDate;
        $date          = $request->date;
        $product_id    = $request->product_id;
        $quantity      = $request->quantity;

        $newArray = [];
        $allProduct = [];
        if(isset($product_id) && count($product_id) > 0){
            for($i = 0; $i < count($product_id); $i++){
                $newArray['productId'] = $product_id[$i];
                $newArray['quantity'] = $quantity[$i];
                array_push($allProduct, $newArray);
            }
        }else{
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->back()->withInput();
        }

        $stockOut = Stockout::select('quantity')->where('date', $oldDate)->where('product_id', $oldProduct_id)->get();
        $oldTotal = 0;
        foreach($stockOut as $stock){
            $oldTotal += $stock->quantity;
        }
        
        try{
            DB::beginTransaction();

            Stockout::where('date', $oldDate)->where('product_id', $oldProduct_id)->delete();

            $stock = Stock::where('product_id', $oldProduct_id)->update(['quantity' => DB::raw('quantity + '.$oldTotal)]);

            foreach($allProduct as $product){
                $stock = Stock::where('product_id', $product['productId'])->update(['quantity' => DB::raw('quantity - '.$product['quantity']), 'updated_by'=>Auth::user()->id]);

                $stockout = new Stockout;
                $stockout->date          = $date;
                $stockout->product_id    = $product['productId'];
                $stockout->quantity      = $product['quantity'];
                $stockout->updated_by    = Auth::user()->id;
                $stockout->save();
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($stockout){
            session()->flash('success','Stock Updated Successfully.');
            return redirect()->route('admin.stockout.list', $date);
        }else{
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function stockoutDate(){
        $title = "Stock Out Date";
        $url = "admin.stockout.list";
        $data = SessionController::stockDate('stockout_history');

        return view('admin.stock.stockDate')->with(['title'=>$title, 'url'=>$url, 'data'=>$data]);
    }
}
