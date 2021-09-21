<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Type;
use App\Model\Category;
use Auth;
use DB;

class AdminController extends Controller
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

    public function index(){
        $title = "Admin Dashboard";
        return view('admin.index')->with(['title'=>$title]);
    }
    
}


/* 
try{
    DB::beginTransaction();
    
    DB::commit();
}catch(Exception $e){
    DB::rollback();
}
*/