<?php

namespace App\Http\Controllers;

use Exception;

use App\Model\Type;
use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct(){

    }

    public function index(){
        $title = "Category List";
        $create_url = "categories.create";
        $create_text = "Category Create";
        $categories = Category::all();

        $all_data = [
            'title'         => $title,
            'create_url'    => $create_url,
            'create_text'   => $create_text,
            'categories'    => $categories,
        ];

        return view('admin.category.list')->with($all_data);
    }

    public function create(){
        $title = "Category Create";
        $types = Type::orderBy('type_name', 'asc')->get();

        $all_data = [
            'title' => $title,
            'types' => $types,
        ];

        return view('admin.category.create')->with($all_data);
    }

    public function store(Request $request){
        $this->validate($request, [
            'type_id' => 'required|integer',
            'category_name' => 'required',
        ]);

        try{
            DB::beginTransaction();

            $category = new Category;
            $category->type_id = $request->type_id;
            $category->category_name = $request->category_name;
            $category->updated_by = auth()->user()->id;
            $category->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$category){
            session()->flash('error', 'Category does not create Successfully.');
            return redirect()->route('categories.create')->withInput();
        }

        return redirect()->route('categories.index');
    }

    public function edit(Category $category){
        $title = "Category Edit";
        $types = Type::orderBy('type_name', 'asc')->get();

        $all_data = [
            'title'     => $title,
            'types'     => $types,
            'category'  => $category,
        ];

        return view('admin.category.edit')->with($all_data);
    }

    public function update(Category $category, Request $request){
        $this->validate($request, [
            'type_id' => 'required|integer',
            'category_name' => 'required',
        ]);

        try{
            DB::beginTransaction();

            $category->type_id        = $request->type_id;
            $category->category_name  = $request->category_name;
            $category->updated_by     = auth()->user()->id;
            $category->save();


            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$category){
            session()->flash('error', 'Category does not update Successfully.');
            return redirect()->route('categories.edit')->withInput();
        }

        return redirect()->route('categories.index');
    }
}
