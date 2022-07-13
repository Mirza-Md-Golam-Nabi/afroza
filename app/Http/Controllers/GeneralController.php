<?php

namespace App\Http\Controllers;

use App\Model\Product;
use App\Model\Category;
use App\Model\Stockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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
        $data = DB::table('stock')
                ->select('product_name','quantity','current_price')
                ->where('product_id', $product_id)
                ->first();
        return ['product_name'=>$data->product_name, 'quantity'=>$data->quantity, 'price'=>$data->current_price];
    }

    public function addMoreDate(Request $request){
        $date = $request->get('date');
        $url = $request->get('url');
        $model = $request->get('model');

        $start_date = $this->startDate($date);
        $end_date = $this->endDate($date);

        $stockoutData = $this->addMoreDataTableWise($model, $end_date, $start_date);

        $data = $this->dataFormat($stockoutData, $url);

        return [
            'data' => $data,
            'end_date' => $end_date,
        ];
    }

    public function startDate($date){
        return date('Y-m-d', strtotime($date . '-1 days'));
    }

    public function endDate($date){
        return date('Y-m-d', strtotime($date . '-1 months'));
    }

    public function dataFormat($data, $url){
        $output = '';
        // change the date format from Y-m-d to d-m-y. e.g - 2021-12-30 to 30-12-21
        foreach($data as $key=>$dat){
            $output .= '<p style="border: 1px solid gray; padding:5px 10px;">';
            $output .= '<a href="' . route($url, $dat->date) . '">' . date("d-m-y", strtotime($dat->date)) . '</a>';
            $output .= '</p>';
        }
        return $output;
    }

    public function addMoreDataTableWise($tableName, $end_date, $start_date){
        return $tableName::select('date')
            ->whereBetween('date', [$end_date, $start_date])
            ->orderBy('date','desc')
            ->groupBy('date')
            ->get();
    }
}
