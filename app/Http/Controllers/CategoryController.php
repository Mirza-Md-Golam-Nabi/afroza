<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Category;
use App\Model\Type;
use Auth;
use DB;

class CategoryController extends Controller
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
    
    public function categoryCreate(){
        $title = "Category Create";
        $typeList = Type::orderBy('type_name', 'asc')->get();
        return view('admin.category.create')->with(['title'=>$title, 'typeList'=>$typeList]);
    }

    public function categoryStore(Request $request){
        $this->validate($request, [
            'type_id' => 'required',
            'category_name' => 'required',
        ]);
        
        try{
            DB::beginTransaction();

            $category = new Category;
            $category->type_id = $request->type_id;
            $category->category_name = $request->category_name;
            $category->updated_by = Auth::user()->id;
            $category->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($category){
            session()->flash('success', 'Category Created Successfully.');
            return redirect()->route('admin.category.create');
        }else{
            session()->flash('error', 'Category does not create Successfully.');
            return redirect()->back()->withInput();
        }
    }

    public function categoryList(){
        $title = "Category List";
        $create_url = "admin.category.create";
        $create_text = "Category Create";
        $categoryList = DB::table('categories as a')
                      ->leftJoin('types as b', 'b.id', '=', 'a.type_id')
                      ->select('a.id', 'a.category_name', 'b.type_name')
                      ->orderBy('b.type_name', 'asc')
                      ->orderBy('a.category_name', 'asc')
                      ->get();

        return view('admin.category.list')->with(['title'=>$title, 'create_url'=>$create_url, 'create_text'=>$create_text, 'categoryList'=>$categoryList]);
    }

    public function categoryEdit($category_id){
        $title = "Category Edit";
        $typeList = Type::orderBy('type_name', 'asc')->get();
        $category = Category::where('id', $category_id)->first();
        return view('admin.category.edit')->with(['title'=>$title, 'typeList'=>$typeList, 'category'=>$category]);
    }

    public function categoryUpdate(Request $request){
        $this->validate($request, [
            'category_id' => 'required',
            'type_id' => 'required',
            'category_name' => 'required',
        ]);


        $category_id = $request->category_id;
        $type_id = $request->type_id;
        $category_name = $request->category_name;

        $categoryData = Category::where('id', $category_id)->first();

        try{
            DB::beginTransaction();

            $categoryData->type_id        = $type_id;
            $categoryData->category_name  = $category_name;
            $categoryData->updated_by     = Auth::user()->id;
            $categoryData->save();


            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }
    
        if($categoryData){
            session()->flash('success', 'Category Updated Successfully.');
            return redirect()->route('admin.category.list');
        }else{
            session()->flash('error', 'Category does not update Successfully.');
            return redirect()->back()->withInput();
        }
    }
}
