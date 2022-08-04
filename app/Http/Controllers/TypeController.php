<?php

namespace App\Http\Controllers;

use Exception;

use App\Model\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeController extends Controller
{
    public function __construct(){

    }

    public function index(){
        $title = "Type List";
        $create_url = "types.create";
        $create_text = "Create Type";
        $types = Type::orderBy('type_name', 'asc')->get();

        $all_data = [
            'title'         => $title,
            'create_url'    => $create_url,
            'create_text'   => $create_text,
            'types'         => $types
        ];

        return view('admin.type.list')->with($all_data);
    }

    public function create(){
        $title = "Type Create";
        return view('admin.type.create')->with(['title'=>$title]);
    }

    public function store(Request $request){
        $this->validate($request, [
            'type_name' => 'required',
        ]);

        try{
            DB::beginTransaction();

            $typeData = new Type;
            $typeData->type_name = $request->type_name;
            $typeData->updated_by = auth()->user()->id;
            $typeData->save();

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$typeData){
            session()->flash('error','Type does not create successfully.');
            return redirect()->route('types.create')->withInput();
        }

        return redirect()->route('types.index');
    }

    public function edit($type_id){
        $title = "Type Edit";
        $type = Type::select('id', 'type_name')->find($type_id);

        $all_data = [
            'title' => $title,
            'type'  => $type,
        ];

        return view('admin.type.edit')->with($all_data);
    }

    public function update($id, Request $request){
        $this->validate($request, [
            'type_name' => 'required',
        ]);

        try{
            DB::beginTransaction();

            $type = Type::where('id', $id)->update([
                'type_name' => $request->type_name,
                'updated_by' => auth()->user()->id
            ]);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if(!$type){
            session()->flash('error','Type does not update successfully.');
            return redirect()->route('types.edit')->withInput();
        }

        return redirect()->route('types.index');
    }

}
