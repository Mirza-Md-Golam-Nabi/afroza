<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Product;
use App\Model\Stock;
use App\Model\Stockin;
use Auth;
use DB;
 
class StockinController extends Controller
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
    
    public function stockinCreate(){
        $title = "Stock In";
        $productList = Product::where('status', 1)->orderBy('product_name', 'asc')->get();
        return view('admin.stock.stockin.create')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function stockinStore(Request $request){
        $this->validate($request, [
            'date' => 'required',
        ]);

        $invoice    = $request->invoice;
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
                $stockin = new Stockin;
                $stockin->invoice_id    = $invoice;
                $stockin->date          = $date;
                $stockin->product_id    = $stock['product_id'];
                $stockin->quantity      = $stock['quantity'];
                $stockin->updated_by    = Auth::user()->id;
                $stockin->save();

                $data = Stock::where('product_id', $stock['product_id'])->update(['quantity'=>DB::raw('quantity + '.$stock["quantity"])]);
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
        $title = "Stock-in History";
        $dataList = DB::table('stockin_history as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', 'a.date', DB::raw('SUM(a.quantity) as quantity'), 'b.product_name')
                ->where('a.date', $date)
                ->groupBy('a.date', 'a.product_id')
                ->orderBy('a.date', 'desc')
                ->get();
                
        return view('admin.stock.stockin.list')->with(['title'=>$title,'dataList'=>$dataList, 'date'=>$date]);
    }

    public function stockinEdit($date, $product_id){
        $title = "Stock In Edit";
        $invoice_id = "";
        $stockinList = Stockin::where('date', $date)->where('product_id', $product_id)->get();
        foreach($stockinList as $stockin){
            $invoice_id = $stockin->invoice_id;
            break;
        }
        $productList = Product::where('status', 1)->orderBy('product_name', 'asc')->get();
        return view('admin.stock.stockin.edit')->with(['title'=>$title, 'stockinList'=>$stockinList,'productList'=>$productList, 'date'=>$date, 'productId'=>$product_id, 'invoice_id'=>$invoice_id]);
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

        if(empty($invoice)){
            $invoice = "N/A";
        }

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

        $stockIn = Stockin::select('quantity')->where('date', $oldDate)->where('product_id', $oldProductId)->get();
        $allTotal = 0;
        foreach($stockIn as $stock){
            $allTotal += $stock->quantity;
        }

        try{
            DB::beginTransaction();
            
            Stockin::where('date', $oldDate)->where('product_id', $oldProductId)->delete();

            $stock = Stock::where('product_id', $oldProductId)->update(['quantity' => DB::raw('quantity - '.$allTotal)]);

            foreach($allProduct as $product){
                Stock::where('product_id', $product['productId'])->update(['quantity' => DB::raw('quantity + '.$product['quantity']), 'updated_by'=>Auth::user()->id]);

                $stockin = new Stockin;
                $stockin->invoice_id    = $invoice;
                $stockin->date          = $date;
                $stockin->product_id    = $product['productId'];
                $stockin->quantity      = $product['quantity'];
                $stockin->updated_by    = Auth::user()->id;
                $stockin->save();
            }   
            
            
            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($stockin){
            session()->flash('success','Stock Updated Successfully.');
            return redirect()->route('admin.stockin.list', $date);
        }else{
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function stockinDate(){
        $title = "Stock In Date";
        $url = "admin.stockin.list";
        $data = SessionController::stockDate('stockin_history');

        return view('admin.stock.stockDate')->with(['title'=>$title, 'url'=>$url, 'data'=>$data]);
    }
}
