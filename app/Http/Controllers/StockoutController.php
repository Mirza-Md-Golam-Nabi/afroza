<?php

namespace App\Http\Controllers;

use Exception;
use App\Model\Stock;
use App\Model\Product;
use App\Model\Stockout;
use App\Model\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperController;

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
        $productList = DB::table('products as a')
                    ->leftJoin('stock as b', 'a.id', '=', 'b.product_id')
                    ->select('a.id', 'a.product_name')
                    ->where('b.quantity', '>', 0)
                    ->orderBy('a.product_name', 'asc')
                    ->get();
        return view('admin.stock.stockout.create')->with(['title'=>$title, 'productList'=>$productList]);
    }

    public function stockoutStore(Request $request){
        $this->validate($request, [
            'date' => 'required',
        ]);

        $date       = $request->date;
        $product_id = $request->product_id;
        $quantity   = $request->quantity;
        $price      = $request->price;

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

            $stockout = $this->stockCalculate($stockData, $date);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($stockout){
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
                ->select('a.product_id', DB::raw('SUM(a.quantity) as quantity'), DB::raw('SUM(a.buying_price) as buy'), DB::raw('SUM(a.selling_price) as sell'), 'b.product_name')
                ->where('a.date', $date)
                ->groupBy('a.date', 'a.product_id')
                ->orderBy('b.product_name', 'asc')
                ->get();

        $outLast = DB::table('stockout_history')->select('updated_at')->where('date', $date)->orderBy('id','desc')->first();
        $lastUpdate = $outLast->updated_at;

        return view('admin.stock.stockout.list')->with(['title'=>$title,'dataList'=>$dataList, 'date'=>$date, 'lastUpdate'=>$lastUpdate]);
    }

    public function stockoutListAll($date)
    {
        $title = "Stock-out History";
        $dataList = DB::table('stockout_history as a')
                ->leftJoin('products as b', 'b.id', '=', 'a.product_id')
                ->select('a.product_id', 'a.quantity as quantity', 'a.buying_price as buy', 'a.selling_price as sell', 'b.product_name')
                ->where('a.date', $date)
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
        $price         = $request->price;

        $allProduct = [];
        if(isset($product_id) && count($product_id) > 0){
            for($i = 0; $i < count($product_id); $i++){
                array_push($allProduct, [
                    'product_id' => $product_id[$i],
                    'quantity'   => $quantity[$i],
                    'price'      => $price[$i],
                ]);
            }
        }else{
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->back()->withInput();
        }

        $stockOut = Stockout::select('quantity', 'selling_price')->where('date', $oldDate)->where('product_id', $oldProduct_id)->get();
        $old_sell_quantity = 0;
        $old_sell_price = 0;
        foreach($stockOut as $stock){
            $old_sell_quantity += $stock->quantity;
            $old_sell_price += $stock->selling_price;
        }
        $pre_bag_sell_price = $old_sell_price / $old_sell_quantity;

        $statusUpdate = 0;
        $product_price = ProductPrice::where('product_id', $oldProduct_id)->where('status', 1)->first();
        if(!$product_price){
            $product_price = ProductPrice::where('product_id', $oldProduct_id)->where('status', 2)->latest()->first();
        }

        $stock = Stock::where('product_id', $oldProduct_id)->first();

        try{
            DB::beginTransaction();

            $avail_stock = $stock->applicable_stock + $old_sell_quantity;
            $avail_price = $product_price->price / $product_price->quantity;

            if($old_sell_quantity > ($product_price->quantity - $stock->applicable_stock)){
                $statusUpdate = 1;
                if($product_price->status == 1){
                    $update = ProductPrice::where('product_id', $oldProduct_id)->where('status', 1)->first();
                }elseif($product_price->status == 2){
                    $update = ProductPrice::where('product_id', $oldProduct_id)->where('status', 2)->latest()->first();
                }
                $sell_quantity = $product_price->quantity - $stock->applicable_stock;
                $sell_price = ceil($pre_bag_sell_price * $sell_quantity);
                $update->status = 0;
                $update->sell_price = $update->sell_price - $sell_price;
                $update->save();
                $remain = $old_sell_quantity - ($product_price->quantity - $stock->applicable_stock);
                $remain_sell_price = $old_sell_price - $sell_price;

                while($remain != 0){
                    $checker = ProductPrice::where('product_id', $oldProduct_id)->where('status', 2)->latest()->first();
                    if($remain > $checker->quantity){
                        $sell_price = ceil($pre_bag_sell_price * $product_price->quantity);
                        $checker->status = 0;
                        $checker->sell_price = $checker->sell_price - $sell_price;
                        $checker->save();
                        $remain -= $checker->quantity;
                        $remain_sell_price -= $sell_price;
                    }else{
                        $avail_stock = ($checker->quantity - $remain) == 0 ? $checker->quantity : $remain;
                        $avail_price = $checker->price / $checker->quantity;
                        $remain = 0;
                        $checker->status = 1;
                        $checker->sell_price = $checker->sell_price - $remain_sell_price;
                        $checker->save();
                    }
                }
            }

            if($statusUpdate == 0){
                $product_price->status = 1;
                $product_price->sell_price = $product_price->sell_price - $old_sell_price;
                $product_price->save();
            }
            Stockout::where('date', $oldDate)->where('product_id', $oldProduct_id)->delete();

            $stock = Stock::where('product_id', $oldProduct_id)->update(['quantity' => DB::raw('quantity + '.$old_sell_quantity), 'current_price'=>$avail_price, 'applicable_stock'=>$avail_stock]);

            $stockout = $this->stockCalculate($allProduct, $date);

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

        $all_data = [
            'title' => $title,
            'url'   => $url,
            'data'  => $data,
            'model' => 'App\\\Model\\\Stockout',
        ];

        return view('admin.stock.stockDate')->with($all_data);
    }

    public function stockCalculate($allProduct, $date){
        foreach($allProduct as $stock){
            $stockCurrent = Stock::select('product_name','quantity','current_price', 'applicable_stock')->where('product_id', $stock['product_id'])->first();
            if($stockCurrent->quantity < $stock['quantity']){
                session()->flash('error', $stockCurrent->product_name.' too much input. Available Stock = '.$stockCurrent->quantity);
                return redirect()->back()->withInput();
            }elseif($stockCurrent->current_price == 0){
                session()->flash('error', $stockCurrent->product_name.' buying price is not available');
                return redirect()->back()->withInput();
            }
            $buyingPrice = 0;
            $avail_stock = $stockCurrent->applicable_stock - $stock['quantity'];
            $avail_price = $stockCurrent->current_price;

            if($stockCurrent->applicable_stock >= $stock['quantity']){
                $buyingPrice = $stock['quantity'] * $stockCurrent->current_price;
                $stock_quantity = $stockCurrent->quantity - $stock['quantity'];

                if($stockCurrent->applicable_stock == $stock['quantity']){
                    DB::table('product_price')->where('product_id', $stock['product_id'])->where('status', 1)->update([
                        'status' => 2,
                        'sell_price' => DB::raw('sell_price + ' . $stock['price'])
                    ]);
                    $priceRate = ProductPrice::where('product_id', $stock['product_id'])->where('status', 0)->first();

                    $avail_stock = 0;
                    $avail_price = 0;

                    if($priceRate){
                        $avail_stock = $priceRate->quantity;
                        $avail_price = $priceRate->price / $priceRate->quantity;

                        $priceRate->status = 1;
                        $priceRate->save();
                    }
                }else{
                    DB::table('product_price')->where('product_id', $stock['product_id'])->where('status', 1)->update([
                        'sell_price' => DB::raw('sell_price + ' . $stock['price'])
                    ]);
                }

                $data = Stock::where('product_id', $stock['product_id'])->update(['quantity'=>$stock_quantity, 'current_price'=>$avail_price, 'applicable_stock'=>$avail_stock, 'updated_by'=>Auth::user()->id]);
            }else{
                $per_bag_price = $stock['price'] / $stock['quantity'];
                DB::table('product_price')->where('product_id', $stock['product_id'])->where('status',1)->update([
                    'status' => 2,
                    'sell_price' => DB::raw('sell_price + ' . ($stockCurrent->applicable_stock * $per_bag_price))
                ]);
                $stkQuantity = $stock['quantity'] - $stockCurrent->applicable_stock;
                $remain_stock = 0;
                $current_price = 0;
                $buyingPrice = $stockCurrent->applicable_stock * $stockCurrent->current_price;
                $i = 0;
                while($stkQuantity != 0 && $i < 5){
                    $i++;
                    $fetch = ProductPrice::where('product_id', $stock['product_id'])->where('status', 0)->first();

                    if($stkQuantity > $fetch->quantity){
                        $buyingPrice += $fetch->price;
                        $stkQuantity -= $fetch->quantity;
                        $fetch->sell_price = ($fetch->quantity * $per_bag_price);
                        $fetch->status = 2;
                        $fetch->save();
                    }elseif($stkQuantity == $fetch->quantity){
                        $buyingPrice += $fetch->price;
                        $stkQuantity -= $fetch->quantity;
                        $fetch->sell_price = ($fetch->quantity * $per_bag_price);
                        $fetch->status = 2;
                        $fetch->save();
                        $fetch = ProductPrice::where('product_id', $stock['product_id'])->where('status', 0)->first();
                        if($fetch){
                            $fetch->status = 1;
                            $fetch->save();
                            $remain_stock = $fetch->quantity;
                            $current_price = $fetch->price / $fetch->quantity;
                        }
                        $stkQuantity = 0;
                    }else{
                        $current_price = $fetch->price / $fetch->quantity;
                        $remain_stock = $fetch->quantity - $stkQuantity;
                        $perProductPrice = $fetch->price / $fetch->quantity;
                        $buyingPrice += ($perProductPrice * $stkQuantity);
                        $fetch->sell_price = ($stkQuantity * $per_bag_price);
                        $fetch->status = 1;
                        $fetch->save();
                        $stkQuantity = 0;
                    }
                }

                $data = DB::table('stock')->where('product_id', $stock['product_id'])->update(['quantity'=>DB::raw('quantity - '.$stock["quantity"]), 'applicable_stock'=>$remain_stock, 'current_price'=>$current_price, 'updated_by'=>Auth::user()->id]);

            }

            $stockout = new Stockout;
            $stockout->date          = $date;
            $stockout->product_id    = $stock['product_id'];
            $stockout->quantity      = $stock['quantity'];
            $stockout->buying_price  = $buyingPrice;
            $stockout->selling_price = $stock['price'];
            $stockout->updated_by    = Auth::user()->id;
            $stockout->save();
        }
        return 1;
    }
}
