<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Type;
use Auth;
use DB; 

class TypeController extends Controller
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
    
    public function typeCreate(){
        $title = "Type Create";
        return view('admin.type.create')->with(['title'=>$title]);
    }

    public function typeStore(Request $request){
        $this->validate($request, [
            'typeName' => 'required',
        ]);
        
        try{
            DB::beginTransaction();

            $typeData = new Type;
            $typeData->type_name = $request->typeName;
            $typeData->updated_by = Auth::user()->id;
            $typeData->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($typeData){
            session()->flash('success', 'Type Create Successfully.');
        }else{
            session()->flash('error','Type does not create successfully.');
        }

        return redirect()->back()->withInput();
    }

    public function typeList(){
        $title = "Type List";
        $create_url = "admin.type.create";
        $create_text = "Create Type";
        $typeList = DB::table('types')->orderBy('type_name', 'asc')->get();
        return view('admin.type.list')->with(['title'=>$title, 'create_url'=>$create_url, 'create_text'=>$create_text, 'typeList'=>$typeList]);
    }

    public function typeEdit($type_id){
        $title = "Type Edit";
        $type = DB::table('types')->select('id', 'type_name')->where('id', $type_id)->first();
        return view('admin.type.edit')->with(['title'=>$title, 'type'=>$type]);
    }

    public function typeUpdate(Request $request){
        $this->validate($request, [
            'type_id'   => 'required',
            'typeName' => 'required',
        ]);

        $type_id = $request->type_id;
        $type_name = $request->typeName;

        $typeData = Type::where('id', $type_id)->first();
        

        try{
            DB::beginTransaction();

            $typeData->type_name = $type_name;
            $typeData->updated_by = Auth::user()->id;
            $typeData->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($typeData){
            session()->flash('success', 'Type Updated Successfully.');
            return redirect()->route('admin.type.list');
        }else{
            session()->flash('error','Type does not update successfully.');
            return redirect()->back()->withInput();
        }        
    }

}
