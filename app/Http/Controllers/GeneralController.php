<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Category;
use App\Model\Product;
use Auth;
use DB;


class GeneralController extends Controller
{

    public function categoryFetch(Request $request){
        $type_id = $request->get('type-id');
        $categoryList = Category::where('type_id', $type_id)->orderBy('category_name', 'asc')->get();
        if(count($categoryList) > 0){
            $output = '<option value="">Please Select One</option>';
            foreach($categoryList as $list){
                $output .= '<option value="'.$list->id.'">'.$list->category_name.'</option>';
            }
        }else{
            $output = '<option value="">No Data Found</option>';
        }
        return $output; 
    }

    public function productCheck(Request $request){
        $product_name = $request->get('product');

        $data = Product::where('product_name', $product_name)->first();
        if($data){
            return '<small class="text-danger">This product already Stored</small>';
        }else{
            return '<small class="text-success">You can store this Product</small>';
        }
    }

    public function stockCheck(Request $request){
        $product_id = $request->get('productID');
        $data = DB::table('stock')->select('quantity','current_price')->where('product_id', $product_id)->first();
        return ['quantity'=>$data->quantity, 'price'=>$data->current_price];
    }
}
