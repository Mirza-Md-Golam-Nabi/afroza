<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Brand;
use App\Model\Category;
use App\Model\Product;
use App\Model\Stock;
use App\Model\Type;
use Auth;
use DB;
use Image;
use File;

class ProductController extends Controller
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
    
    public function productCreate(){
        $title = "Product Create";
        $typeList = Type::orderBy('type_name', 'asc')->get();
        $brandList = Brand::where('status', 1)->orderBy('brand_name', 'asc')->get();

        return view('admin.product.create')->with(['title'=>$title, 'typeList'=>$typeList, 'brandList'=>$brandList]);
    }

    public function productStore(Request $request){
        $this->validate($request, [
            'type_id'       => 'required',
            'category_id'   => 'required',
            'brand_id'      => 'required',
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
                $newArray = [];
                $newArray['unit_value'] = $others_unit_value[$i];
                $newArray['unit_name'] = $others_unit_name[$i];
                array_push($othersUnit, $newArray);
            }
        }
        
        $newArray = [];
        $newArray['unit_value'] = 1;
        $newArray['unit_name'] = $main_unit;
        array_push($othersUnit, $newArray);

        $othersUnit = json_encode($othersUnit);        

        try{
            DB::beginTransaction();

            $productData = new Product;
            $productData->type_id           = $type_id;
            $productData->category_id       = $category_id;
            $productData->brand_id          = $brand_id;
            $productData->product_name      = $product_name;
            $productData->main_unit         = $main_unit;
            $productData->others_unit       = $othersUnit;
            $productData->warning           = $warning;
            $productData->updated_by        = Auth::user()->id;
            $productData->save();

            $stockData = new Stock;
            $stockData->type_id         = $type_id;
            $stockData->category_id     = $category_id;
            $stockData->brand_id        = $brand_id;
            $stockData->product_id      = $productData->id;
            $stockData->product_name    = $product_name;
            $stockData->quantity        = 0;
            $stockData->unit            = $main_unit;
            $stockData->warning         = $warning;
            $stockData->updated_by      = Auth::user()->id;
            $stockData->save();
            
            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($productData){
            session()->flash('success','Product Added Successfully.');
            return redirect()->route('admin.product.create');
        }else{
            session()->flash('error','Product does not Added successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function productList(){
        $title = "Product List";
        $create_url = "admin.product.create";
        $create_text = "Create Product";
        $productList = DB::table('products as a')
                    ->leftJoin('categories as c', 'c.id', '=', 'a.category_id')
                    ->leftJoin('types as d', 'd.id', '=', 'a.type_id')                    
                    ->leftJoin('brands as e', 'e.id', '=', 'a.brand_id')
                    ->select('a.id', 'a.product_name', 'a.main_unit', 'a.others_unit', 'a.warning', 'a.status', 'c.category_name', 'd.type_name', 'e.brand_name')
                    ->orderBy('a.status', 'desc')
                    ->orderBy('d.type_name', 'asc')
                    ->orderBy('c.category_name', 'asc')
                    ->orderBy('a.product_name', 'asc')
                    ->get();
        return view('admin.product.list')->with(['title'=>$title, 'create_url'=>$create_url, 'create_text'=>$create_text, 'productList'=>$productList]);
    }

    public function productEdit($product_id){
        $title = "Product Edit";
        $product = Product::where('id', $product_id)->first();
        $typeList = Type::orderBy('type_name', 'asc')->get();
        $brandList = Brand::where('status', 1)->orderBy('brand_name', 'asc')->get();
        $categoryList = Category::where('type_id', $product->type_id)->orderBy('category_name', 'asc')->get();
        return view('admin.product.edit')->with(['title'=>$title, 'product'=>$product, 'typeList'=>$typeList, 'brandList'=>$brandList, 'categoryList'=>$categoryList]);
    }

    public function productUpdate(Request $request){
        $this->validate($request, [
            'product_id'    => 'required',
            'type_id'       => 'required',
            'brand_id'      => 'required',
            'category_id'   => 'required',
            'product_name'  => 'required',
            'main_unit'     => 'required',
        ]);

        $product_id     = $request->product_id;
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
                $newArray = [];
                $newArray['unit_value'] = $others_unit_value[$i];
                $newArray['unit_name'] = $others_unit_name[$i];
                array_push($othersUnit, $newArray);
            }
        }
        $newArray = [];
        $newArray['unit_value'] = 1;
        $newArray['unit_name'] = $main_unit;
        array_push($othersUnit, $newArray);
        
        $othersUnit = json_encode($othersUnit);

        $productData = Product::where('id', $product_id)->first();
        $stockData = Stock::where('product_id', $product_id)->first();

        try{
            DB::beginTransaction();

            $productData->type_id           = $type_id;
            $productData->category_id       = $category_id;
            $productData->brand_id          = $brand_id;
            $productData->product_name      = $product_name;
            $productData->main_unit         = $main_unit;
            $productData->others_unit       = $othersUnit;
            $productData->warning           = $warning;
            $productData->updated_by        = Auth::user()->id;
            $productData->save();

            $stockData->type_id         = $type_id;
            $stockData->category_id     = $category_id;
            $stockData->brand_id        = $brand_id;
            $stockData->product_id      = $product_id;
            $stockData->product_name    = $product_name;
            $stockData->quantity        = $stockData->quantity;
            $stockData->unit            = $main_unit;
            $stockData->warning         = $warning;
            $stockData->updated_by      = Auth::user()->id;
            $stockData->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($productData){
            session()->flash('success','Product Updated Successfully.');
            return redirect()->route('admin.product.list');
        }else{
            session()->flash('error', 'Product does not update Successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function productStatus(Request $request, $productId){
        $status = $request->get('status');

        try{
            DB::beginTransaction();

            $update = DB::table('products')->where('id', $productId)->update(['status'=>$status, 'updated_by'=>Auth::user()->id]);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }
        if($update){
            session()->flash("success", "Product Updated Successfully.");
        }else{
            session()->flash("error", "Something Error.");
        }
        return redirect()->back();
    }
}
