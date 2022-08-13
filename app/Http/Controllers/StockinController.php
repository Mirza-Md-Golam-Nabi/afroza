<?php

namespace App\Http\Controllers;

use Exception;

use App\Model\Stock;
use App\Model\Product;
use App\Model\Stockin;
use App\Model\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockinController extends Controller
{
    public function stockinDate(){
        $title = "Stock In Date";
        $url = "stockins.index";
        $data = SessionController::stockDate('stockin_history');

        $all_data = [
            'title' => $title,
            'url'   => $url,
            'data'  => $data,
            'model' => 'App\\\Model\\\Stockin',
        ];

        return view('admin.stock.stockDate')->with($all_data);
    }

    public function index(Request $request){
        $date = $request->get('date');
        $title = "Stock-in History by Group";

        $stockin = new Stockin();
        $stocks = $stockin->dateWiseGroupProduct($date);
        $lastUpdate = $stockin->lastUpdateTimeForAll($date);

        $all_data = [
            'title'      => $title,
            'stocks'     => $stocks,
            'date'       => $date,
            'lastUpdate' => $lastUpdate,
        ];

        return view('admin.stock.stockin.list')->with($all_data);
    }

    public function create(){
        $title = "Stock In";
        $product = new Product();
        $products = $product->activeAll();

        $all_data = [
            'title'    => $title,
            'products' => $products,
        ];

        return view('admin.stock.stockin.create')->with($all_data);
    }

    public function store(Request $request){
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

        if(
            count($product_id) != count($quantity) ||
            count($quantity) != count($price) ||
            count($price) != count($product_id)
        ){
            session()->flash('error','Product, Quantity and Price set are not same');
            return redirect()->route('stockins.create')->withInput();
        }

        $stocks = [];
        for($i = 0; $i < count($product_id); $i++){
            array_push($stocks, [
                'product_id' => $product_id[$i],
                'quantity'   => $quantity[$i],
                'price'      => $price[$i],
            ]);
        }

        try{
            DB::beginTransaction();

            foreach($stocks as $stock){
                $stockin = new Stockin;
                $stockin->invoice_id    = $invoice;
                $stockin->date          = $date;
                $stockin->product_id    = $stock['product_id'];
                $stockin->quantity      = $stock['quantity'];
                $stockin->buying_price  = $stock['price'];
                $stockin->updated_by    = auth()->user()->id;
                $stockin->save();

                $stock_data = Stock::where('product_id', $stock['product_id'])->update([
                        'quantity'   => DB::raw('quantity + ' . $stock["quantity"]),
                        'updated_by' => auth()->user()->id,
                    ]);

                $productPrice = new ProductPrice();

                $productPriceStatus = 0;

                if(!$productPrice->productHas($stock['product_id'])){
                    $stock_data->current_price = $stock['price'];
                    $stock_data->applicable_stock = $stock['quantity'];
                    $stock_data->save();

                    $productPriceStatus = 1;
                }

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

        if($stock_data){
            session()->flash('success','Stock Added Successfully.');
            return redirect()->route('stockins.create');
        }else{
            session()->flash('error','Stock does not Added successfully.');
            return redirect()->route('stockins.create')->withInput();
        }
    }

    public function stockinListAll(Request $request){
        $date = $request->get('date');
        $title = "Stock-in History by All";

        $stockin = new Stockin();
        $stocks = $stockin->dateWiseAllProduct($date);
        $lastUpdate = $stockin->lastUpdateTimeForAll($date);

        $all_data = [
            'title'      => $title,
            'stocks'     => $stocks,
            'date'       => $date,
            'lastUpdate' => $lastUpdate,
        ];

        return view('admin.stock.stockin.list')->with($all_data);
    }

    public function edit($product_id, Request $request){
        $date = $request->get('date');
        $title = "Stock In Edit";

        $stockin = new Stockin();
        $stocks = $stockin->dateWiseSingleProduct(['date'=>$date, 'product_id'=>$product_id]);

        $product = new Product();
        $products = $product->activeAll();

        $all_data = [
            'title'     => $title,
            'stocks'    => $stocks,
            'products'  => $products,
            'date'      => $date,
            'productId' => $product_id,
        ];

        return view('admin.stock.stockin.edit')->with($all_data);
    }

    public function update($oldProductId, Request $request){
        $this->validate($request, [
            'oldDate'       => 'required',
            'date'          => 'required',
        ]);

        $oldDate    = $request->oldDate;
        $invoice    = $request->invoice;
        $date       = $request->date;
        $product_id = $request->product_id;
        $quantity   = $request->quantity;
        $price      = $request->price;

        if(empty($invoice)){
            $invoice = "N/A";
        }

        if(
            count($product_id) != count($quantity) ||
            count($quantity) != count($price) ||
            count($price) != count($product_id)
        ){
            session()->flash('error', 'Product, Quantity and Price set are not same');
            return redirect()->route('stockins.edit', [$oldProductId, 'date' => $oldDate])->withInput();
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
            return redirect()->route('stockins.edit', [$oldProductId, 'date' => $oldDate])->withInput();
        }

        $stockIn = Stockin::select('quantity')->where('date', $oldDate)->where('product_id', $oldProductId)->get();
        $allTotal = $stockIn ? $stockIn->sum('quantity') : 0;

        try{
            DB::beginTransaction();

            Stockin::where('date', $oldDate)->where('product_id', $oldProductId)->delete();
            ProductPrice::where('date', $oldDate)->where('product_id', $oldProductId)->delete();

            $stock = Stock::where('product_id', $oldProductId)->update([
                'quantity' => DB::raw('quantity - ' . $allTotal)
            ]);

            foreach($allProduct as $product){
                $productPrice = new ProductPrice();
                $productPriceStatus = 0;

                $stock = Stock::where('product_id', $product['productId'])->update([
                    'quantity' => DB::raw('quantity + ' . $product['quantity']),
                    'updated_by' => auth()->user()->id
                ]);

                if(!$productPrice->productHas($product['productId'])){
                    $productPriceStatus = 1;
                    $stock->current_price = $product['price'];
                    $stock->applicable_stock = $product['quantity'];
                    $stock->save();
                }

                $productPrice->date       = $date;
                $productPrice->product_id = $product['productId'];
                $productPrice->quantity   = $product['quantity'];
                $productPrice->price      = $product['quantity'] * $product['price'];
                $productPrice->status     = $productPriceStatus;
                $productPrice->save();

                $stockin = new Stockin;
                $stockin->invoice_id   = $invoice;
                $stockin->date         = $date;
                $stockin->product_id   = $product['productId'];
                $stockin->quantity     = $product['quantity'];
                $stockin->buying_price = $product['price'];
                $stockin->updated_by   = auth()->user()->id;
                $stockin->save();
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$stockin){
            session()->flash('error','Stock does not Update successfully.');
            return redirect()->route('stockins.edit', [$oldProductId, 'date' => $date])->withInput();
        }

        session()->flash('success','Stock Updated Successfully.');
        return redirect()->route('stockins.index', ['date' => $date]);
    }
}
