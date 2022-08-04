<?php

namespace App\Http\Controllers;

use Exception;

use App\Model\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function __construct(){

    }

    public function index(){
        $title = "Brand List";
        $create_url = "brands.create";
        $create_text = "Create Brand";
        $brands = Brand::orderBy('brand_name', 'asc')->get();

        $all_data = [
            'title'         => $title,
            'create_url'    => $create_url,
            'create_text'   => $create_text,
            'brands'        => $brands,
        ];

        return view('admin.brand.list')->with($all_data);
    }

    public function create(){
        $title = "Brand Create";
        return view('admin.brand.create')->with(['title'=>$title]);
    }

    public function store(Request $request){
        $this->validate($request, [
            'brand_name' => 'required',
        ]);

        try{
            DB::beginTransaction();

            $brand = new Brand;
            $brand->brand_name = $request->brand_name;
            $brand->updated_by = auth()->user()->id;
            $brand->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$brand){
            session()->flash('error','Brand does not create successfully.');
            return redirect()->route('brands.create')->withInput();
        }

        return redirect()->route('brands.index');
    }

    public function edit(Brand $brand){
        $title = "Brand Edit";

        $all_data = [
            'title' => $title,
            'brand' => $brand,
        ];
        return view('admin.brand.edit')->with($all_data);
    }

    public function update(Brand $brand, Request $request){
        $this->validate($request, [
            'brand_name' => 'required',
        ]);

        try{
            DB::beginTransaction();

            $brand->brand_name = $request->brand_name;
            $brand->updated_by = auth()->user()->id;
            $brand->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$brand){
            session()->flash('error','Brand does not update successfully.');
            return redirect()->route('brands.edit')->withInput();
        }

        return redirect()->route('brands.index');
    }
}
