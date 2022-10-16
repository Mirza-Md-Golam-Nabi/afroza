<?php

namespace App\Http\Controllers;

use Exception;
use App\Model\Stock;
use App\Model\Product;
use App\Model\Stockout;
use App\Model\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockoutController extends Controller
{
    public function stockoutDate()
    {
        $title = "Stock Out Date";
        $url = "stockouts.index";
        $data = SessionController::stockDate('stockout_history');

        $all_data = [
            'title' => $title,
            'url'   => $url,
            'data'  => $data,
            'model' => 'App\\\Model\\\Stockout',
        ];

        return view('admin.stock.stockDate')->with($all_data);
    }

    public function index(Request $request)
    {
        $date = $request->get('date');
        $title = "Stock-out History";

        $stockout = new Stockout();
        $products = $stockout->dateWiseGroupProduct($date);
        $lastUpdate = $stockout->lastUpdateTimeForAll($date);

        $all_data = [
            'title'      => $title,
            'products'   => $products,
            'date'       => $date,
            'lastUpdate' => $lastUpdate,
        ];
        return view('admin.stock.stockout.list')->with($all_data);
    }

    public function create()
    {
        $title = "Stock Out";
        $stock = new Stock();
        $products = $stock->availableProducts();

        $all_data = [
            'title' => $title,
            'products' => $products,
        ];

        return view('admin.stock.stockout.create')->with($all_data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);

        $date       = $request->date;
        $product_id = $request->product_id;
        $quantity   = $request->quantity;
        $price      = $request->price;

        if (
            count($product_id) != count($quantity) ||
            count($quantity) != count($price) ||
            count($price) != count($product_id)
        ) {
            session()->flash('error', 'Product, Quantity and Price set are not same');
            return redirect()->route('stockouts.create')->withInput();
        }

        $stockData = [];
        $product_count = count($product_id);
        for ($i = 0; $i < $product_count; $i++) {
            array_push($stockData, [
                'product_id' => $product_id[$i],
                'quantity'   => $quantity[$i],
                'price'      => $price[$i],
            ]);
        }

        try {
            DB::beginTransaction();

            $stockout = $this->stockCalculate($stockData, $date);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        if ($stockout) {
            session()->flash('success', 'Stock out Successfully.');
        } else {
            session()->flash('error', 'Stock does not out successfully.');
        }
        return redirect()->route('stockouts.create');
    }

    public function stockoutListAll(Request $request)
    {
        $date = $request->get('date');
        $title = "Stock-out History";

        $stockout = new Stockout();
        $products = $stockout->dateWiseAllProduct($date);
        $lastUpdate = $stockout->lastUpdateTimeForAll($date);

        $all_data = [
            'title'      => $title,
            'products'   => $products,
            'date'       => $date,
            'lastUpdate' => $lastUpdate,
        ];

        return view('admin.stock.stockout.list')->with($all_data);
    }

    public function edit($product_id, Request $request)
    {
        $date = $request->get('date');
        $title = "Stock Out Edit";

        $stockout = new Stockout();
        $stockouts = $stockout->singleProductDateWise($date, $product_id);

        $product = new Product();
        $products = $product->activeAll();

        $all_data = [
            'title'         => $title,
            'stockouts'     => $stockouts,
            'products'      => $products,
            'date'          => $date,
            'oldProduct_id' => $product_id,
        ];

        return view('admin.stock.stockout.edit')->with($all_data);
    }

    public function update($oldProduct_id, Request $request)
    {
        $this->validate($request, [
            'oldDate' => 'required',
            'date'    => 'required',
        ]);

        $oldDate     = $request->oldDate;
        $date        = $request->date;
        $product_id  = $request->product_id;
        $quantity    = $request->quantity;
        $price       = $request->price;

        if (
            count($product_id) != count($quantity) ||
            count($quantity) != count($price) ||
            count($price) != count($product_id)
        ) {
            session()->flash('error', 'Product, Quantity and Price set are not same');
            return redirect()->route('stockouts.edit', [$oldProduct_id, 'date' => $oldDate]);
        }

        $allProduct = [];
        if (isset($product_id) && count($product_id) > 0) {
            $product_count = count($product_id);
            for ($i = 0; $i < $product_count; $i++) {
                array_push($allProduct, [
                    'product_id' => $product_id[$i],
                    'quantity'   => $quantity[$i],
                    'price'      => $price[$i],
                ]);
            }
        } else {
            session()->flash('error', 'Stock does not Update successfully.');
            return redirect()->route('stockouts.edit', [$oldProduct_id, 'date' => $oldDate]);
        }

        $stockout = new Stockout();
        $singleProduct = $stockout->singleProductDateWise($oldDate, $oldProduct_id);

        $old_sell_quantity = $singleProduct ? $singleProduct->sum('quantity') : 0;
        $old_sell_price = $singleProduct ? $singleProduct->sum('selling_price') : 0;

        $pre_bag_sell_price = $old_sell_price / $old_sell_quantity;

        $productPrice = new ProductPrice();
        $product_price = $productPrice->activeProduct($oldProduct_id);

        if (!$product_price) {
            $product_price = $productPrice->lastDeactivateProduct($oldProduct_id);
        }

        $stock = new Stock();
        $currentProduct = $stock->currentProduct($oldProduct_id);

        $statusUpdate = 0;

        try {
            DB::beginTransaction();

            $avail_stock = $currentProduct->applicable_stock + $old_sell_quantity;
            $avail_price = $product_price->price / $product_price->quantity;

            if ($old_sell_quantity > ($product_price->quantity - $currentProduct->applicable_stock)) {
                $statusUpdate = 1;
                if ($product_price->status == 1) {
                    $update = $productPrice->activeProduct($oldProduct_id);
                } elseif ($product_price->status == 2) {
                    $update = $productPrice->lastDeactivateProduct($oldProduct_id);
                }
                $sell_quantity = $product_price->quantity - $currentProduct->applicable_stock;
                $sell_price = ceil($pre_bag_sell_price * $sell_quantity);
                $update->status = 0;
                $update->sell_price = $update->sell_price - $sell_price;
                $update->save();
                $remain = $old_sell_quantity - ($product_price->quantity - $currentProduct->applicable_stock);
                $remain_sell_price = $old_sell_price - $sell_price;

                while ($remain != 0) {
                    $checker = $productPrice->lastDeactivateProduct($oldProduct_id);
                    if ($remain > $checker->quantity) {
                        $sell_price = ceil($pre_bag_sell_price * $product_price->quantity);
                        $checker->status = 0;
                        $checker->sell_price = $checker->sell_price - $sell_price;
                        $checker->save();
                        $remain -= $checker->quantity;
                        $remain_sell_price -= $sell_price;
                    } else {
                        $avail_stock = ($checker->quantity - $remain) == 0 ? $checker->quantity : $remain;
                        $avail_price = $checker->price / $checker->quantity;
                        $remain = 0;
                        $checker->status = 1;
                        $checker->sell_price = $checker->sell_price - $remain_sell_price;
                        $checker->save();
                    }
                }
            }

            if ($statusUpdate == 0) {
                $product_price->status = 1;
                $product_price->sell_price = $product_price->sell_price - $old_sell_price;
                $product_price->save();
            }

            $stockout->deleteProductDateWise($oldDate, $oldProduct_id);

            $currentProduct->quantity = $currentProduct->quantity + $old_sell_quantity;
            $currentProduct->current_price = $avail_price;
            $currentProduct->applicable_stock = $avail_stock;
            $currentProduct->save();

            $success = $this->stockCalculate($allProduct, $date);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        if (!$success) {
            session()->flash('error', 'Stock does not Update successfully.');
            return redirect()->route('stockouts.edit', [$oldProduct_id, 'date' => $oldDate]);
        }
        session()->flash('success', 'Stock Updated Successfully.');
        return redirect()->route('stockouts.index', ['date' => $date]);
    }

    public function stockCalculate($allProduct, $date)
    {
        foreach ($allProduct as $stock) {
            $stockCurrent = Stock::select('product_name', 'quantity', 'current_price', 'applicable_stock')->where('product_id', $stock['product_id'])->first();
            if ($stockCurrent->quantity < $stock['quantity']) {
                session()->flash('error', $stockCurrent->product_name . ' too much input. Available Stock = ' . $stockCurrent->quantity);
                return redirect()->back()->withInput();
            } elseif ($stockCurrent->current_price == 0) {
                session()->flash('error', $stockCurrent->product_name . ' buying price is not available');
                return redirect()->back()->withInput();
            }
            $buyingPrice = 0;
            $avail_stock = $stockCurrent->applicable_stock - $stock['quantity'];
            $avail_price = $stockCurrent->current_price;

            if ($stockCurrent->applicable_stock >= $stock['quantity']) {
                $buyingPrice = $stock['quantity'] * $stockCurrent->current_price;
                $stock_quantity = $stockCurrent->quantity - $stock['quantity'];

                if ($stockCurrent->applicable_stock == $stock['quantity']) {
                    ProductPrice::where('product_id', $stock['product_id'])->where('status', 1)->update([
                        'status' => 2,
                        'sell_price' => DB::raw('sell_price + ' . $stock['price'])
                    ]);
                    $priceRate = ProductPrice::where('product_id', $stock['product_id'])->where('status', 0)->first();

                    $avail_stock = 0;
                    $avail_price = 0;

                    if ($priceRate) {
                        $avail_stock = $priceRate->quantity;
                        $avail_price = $priceRate->price / $priceRate->quantity;

                        $priceRate->status = 1;
                        $priceRate->save();
                    }
                } else {
                    ProductPrice::where('product_id', $stock['product_id'])->where('status', 1)->update([
                        'sell_price' => DB::raw('sell_price + ' . $stock['price'])
                    ]);
                }

                $data = Stock::where('product_id', $stock['product_id'])->update([
                    'quantity' => $stock_quantity,
                    'current_price' => $avail_price,
                    'applicable_stock' => $avail_stock,
                    'updated_by' => auth()->user()->id
                ]);
            } else {
                $per_bag_price = $stock['price'] / $stock['quantity'];
                ProductPrice::where('product_id', $stock['product_id'])->where('status', 1)->update([
                    'status' => 2,
                    'sell_price' => DB::raw('sell_price + ' . ($stockCurrent->applicable_stock * $per_bag_price))
                ]);
                $stkQuantity = $stock['quantity'] - $stockCurrent->applicable_stock;
                $remain_stock = 0;
                $current_price = 0;
                $buyingPrice = $stockCurrent->applicable_stock * $stockCurrent->current_price;
                $i = 0;
                while ($stkQuantity != 0 && $i < 5) {
                    $i++;
                    $fetch = ProductPrice::where('product_id', $stock['product_id'])->where('status', 0)->first();

                    if ($stkQuantity > $fetch->quantity) {
                        $buyingPrice += $fetch->price;
                        $stkQuantity -= $fetch->quantity;
                        $fetch->sell_price = ($fetch->quantity * $per_bag_price);
                        $fetch->status = 2;
                        $fetch->save();
                    } elseif ($stkQuantity == $fetch->quantity) {
                        $buyingPrice += $fetch->price;
                        $stkQuantity -= $fetch->quantity;
                        $fetch->sell_price = ($fetch->quantity * $per_bag_price);
                        $fetch->status = 2;
                        $fetch->save();
                        $fetch = ProductPrice::where('product_id', $stock['product_id'])->where('status', 0)->first();
                        if ($fetch) {
                            $fetch->status = 1;
                            $fetch->save();
                            $remain_stock = $fetch->quantity;
                            $current_price = $fetch->price / $fetch->quantity;
                        }
                        $stkQuantity = 0;
                    } else {
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

                $data = Stock::where('product_id', $stock['product_id'])->update([
                    'quantity' => DB::raw('quantity - ' . $stock["quantity"]),
                    'applicable_stock' => $remain_stock,
                    'current_price' => $current_price,
                    'updated_by' => auth()->user()->id
                ]);
            }

            $stockout = new Stockout;
            $stockout->date          = $date;
            $stockout->product_id    = $stock['product_id'];
            $stockout->quantity      = $stock['quantity'];
            $stockout->buying_price  = $buyingPrice;
            $stockout->selling_price = $stock['price'];
            $stockout->updated_by    = auth()->user()->id;
            $stockout->save();
        }
        return 1;
    }
}
