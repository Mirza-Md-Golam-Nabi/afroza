<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Brand;
use Auth;
use DB;

class BrandController extends Controller
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
    
    public function brandCreate(){
        $title = "Brand Create";
        return view('admin.brand.create')->with(['title'=>$title]);
    }

    public function brandStore(Request $request){
        $this->validate($request, [
            'brandName' => 'required',
        ]);
        
        try{
            DB::beginTransaction();

            $brandData = new Brand;
            $brandData->brand_name = $request->brandName;
            $brandData->updated_by = Auth::user()->id;
            $brandData->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($brandData){
            session()->flash('success', 'Brand Create Successfully.');
        }else{
            session()->flash('error','Brand does not create successfully.');
        }

        return redirect()->back()->withInput();
    }

    public function brandList(){
        $title = "Brand List";
        $create_url = "admin.brand.create";
        $create_text = "Create Brand";
        $brandList = DB::table('brands')->orderBy('brand_name', 'asc')->get();
        return view('admin.brand.list')->with(['title'=>$title, 'create_url'=>$create_url, 'create_text'=>$create_text, 'brandList'=>$brandList]);
    }

    public function brandEdit($brand_id){
        $title = "Brand Edit";
        $brand = DB::table('brands')->select('id', 'brand_name')->where('id', $brand_id)->first();
        return view('admin.brand.edit')->with(['title'=>$title, 'brand'=>$brand]);
    }

    public function brandUpdate(Request $request){
        $this->validate($request, [
            'brand_id'   => 'required',
            'brandName' => 'required',
        ]);

        $brand_id = $request->brand_id;
        $brand_name = $request->brandName;

        $brandData = brand::where('id', $brand_id)->first();
        

        try{
            DB::beginTransaction();

            $brandData->brand_name = $brand_name;
            $brandData->updated_by = Auth::user()->id;
            $brandData->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($brandData){
            session()->flash('success', 'Brand Updated Successfully.');
            return redirect()->route('admin.brand.list');
        }else{
            session()->flash('error','Brand does not update successfully.');
            return redirect()->back()->withInput();
        }        
    }
}
