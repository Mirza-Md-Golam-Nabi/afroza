<?php

namespace App\Http\Controllers;

use Exception;
use App\Model\Type;
use App\Model\Brand;
use App\Model\Stock;
use App\Model\Product;
use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(){

    }

    public function index(){
        $title = "Product List";
        $create_url = "products.create";
        $create_text = "Create Product";
        $products = Product::orderBy('status', 'desc')->get();

        $all_data = [
            'title'         => $title,
            'create_url'    => $create_url,
            'create_text'   => $create_text,
            'products'      => $products,
        ];

        return view('admin.product.list')->with($all_data);
    }

    public function create(){
        $title = "Product Create";
        $types = Type::orderBy('type_name', 'asc')->get();
        $brands = Brand::where('status', 1)->orderBy('brand_name', 'asc')->get();

        $all_data = [
            'title'  => $title,
            'types'  => $types,
            'brands' => $brands,
        ];

        return view('admin.product.create')->with($all_data);
    }

    public function store(Request $request){
        $this->validate($request, [
            'type_id'       => 'required|integer',
            'category_id'   => 'required|integer',
            'brand_id'      => 'required|integer',
            'product_name'  => 'required',
            'main_unit'     => 'required',
        ]);

        $type_id        = $request->type_id;
        $category_id    = $request->category_id;
        $brand_id       = $request->brand_id;
        $product_name   = $request->product_name;
        $main_unit      = $request->main_unit;
        $warning        = $request->warning;

        if(empty($warning)){
            $warning = 0;
        }

        $others_unit_value = $request->others_unit_value;
        $others_unit_name  = $request->others_unit_name;

        $othersUnit = [];
        if($others_unit_value[0]){
            for($i=0; $i < count($others_unit_value); $i++){
                array_push($othersUnit, [
                    'unit_value' => $others_unit_value[$i],
                    'unit_name'  => $others_unit_name[$i],
                ]);
            }
        }

        array_push($othersUnit, [
            'unit_value' => 1,
            'unit_name'  => $main_unit,
        ]);

        $othersUnit = json_encode($othersUnit);

        try{
            DB::beginTransaction();

            $product = new Product;
            $product->type_id       = $type_id;
            $product->category_id   = $category_id;
            $product->brand_id      = $brand_id;
            $product->product_name  = $product_name;
            $product->main_unit     = $main_unit;
            $product->others_unit   = $othersUnit;
            $product->warning       = $warning;
            $product->updated_by    = auth()->user()->id;
            $product->save();

            $stock = new Stock;
            $stock->type_id         = $type_id;
            $stock->category_id     = $category_id;
            $stock->brand_id        = $brand_id;
            $stock->product_id      = $product->id;
            $stock->product_name    = $product_name;
            $stock->quantity        = 0;
            $stock->unit            = $main_unit;
            $stock->warning         = $warning;
            $stock->current_price   = 0;
            $stock->applicable_stock= 0;
            $stock->updated_by      = auth()->user()->id;
            $stock->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$stock){
            session()->flash('error','Product does not Added successfully.');
            return redirect()->route('products.create')->withInput();
        }
        return redirect()->route('products.index');
    }

    public function edit(Product $product){
        $title = "Product Edit";
        $types = Type::orderBy('type_name', 'asc')->get();
        $brands = Brand::where('status', 1)->orderBy('brand_name', 'asc')->get();
        $categories = Category::where('type_id', $product->type_id)->orderBy('category_name', 'asc')->get();

        $all_data = [
            'title'      => $title,
            'product'    => $product,
            'types'      => $types,
            'brands'     => $brands,
            'categories' => $categories,
        ];

        return view('admin.product.edit')->with($all_data);
    }

    public function update(Product $product, Request $request){
        $this->validate($request, [
            'type_id'       => 'required|integer',
            'brand_id'      => 'required|integer',
            'category_id'   => 'required|integer',
            'product_name'  => 'required',
            'main_unit'     => 'required',
        ]);

        $type_id        = $request->type_id;
        $category_id    = $request->category_id;
        $brand_id       = $request->brand_id;
        $product_name   = $request->product_name;
        $main_unit      = $request->main_unit;
        $warning        = $request->warning;

        if(empty($warning)){
            $warning = 0;
        }

        $others_unit_value = $request->others_unit_value;
        $others_unit_name  = $request->others_unit_name;

        $othersUnit = [];
        if(isset($others_unit_name) && count($others_unit_value) > 0){
            for($i=0; $i < count($others_unit_value); $i++){
                array_push($othersUnit, [
                    'unit_value' => $others_unit_value[$i],
                    'unit_name'  => $others_unit_name[$i],
                ]);
            }
        }

        array_push($othersUnit, [
            'unit_value' => 1,
            'unit_name'  => $main_unit,
        ]);

        $othersUnit = json_encode($othersUnit);

        $stock = Stock::where('product_id', $product->id)->first();

        try{
            DB::beginTransaction();

            $product->type_id           = $type_id;
            $product->category_id       = $category_id;
            $product->brand_id          = $brand_id;
            $product->product_name      = $product_name;
            $product->main_unit         = $main_unit;
            $product->others_unit       = $othersUnit;
            $product->warning           = $warning;
            $product->updated_by        = auth()->user()->id;
            $product->save();

            $stock->type_id         = $type_id;
            $stock->category_id     = $category_id;
            $stock->brand_id        = $brand_id;
            $stock->product_id      = $product->id;
            $stock->product_name    = $product_name;
            $stock->quantity        = $stock->quantity;
            $stock->unit            = $main_unit;
            $stock->warning         = $warning;
            $stock->updated_by      = auth()->user()->id;
            $stock->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$stock){
            session()->flash('error', 'Product does not update Successfully.');
            return redirect()->route('products.edit')->withInput();
        }
        return redirect()->route('products.index');
    }

    public function productStatus(Request $request){
        $productId = $request->get('id');
        $status = $request->get('status');

        try{
            DB::beginTransaction();

            $update = Product::where('id', $productId)->update([
                'status'    => $status,
                'updated_by'=> auth()->user()->id,
            ]);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }
        if($update){
            session()->flash("success", "Product Updated Successfully.");
        }else{
            session()->flash("error", "Something Error.");
        }
        return redirect()->route('products.index');
    }
}
