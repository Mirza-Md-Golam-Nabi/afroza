<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;
use DB;
use Auth;
 
class RegisterController extends Controller
{
    public function userStore(Request $request){
        $this->validate($request, [
            'mobileNumber' => 'required|digits:11',
            'password'     => 'required',
        ]);
        
        $data = ['mobile'  => $request->mobileNumber, 'password' => $request->password];
        Auth::attempt($data);
        if(isset(Auth::user()->group_id) && (Auth::user()->group_id == 1)){
            return redirect()->route('admin.dashboard');
        }else{
            Auth::logout();
            return redirect()->route('welcome');
        }
        
    }

    public function passForget(){
        return view('auth.passwords.reset');
    }

    public function mobileCheck(Request $request){
        $mobile = $request->get('mobile');
        $data = User::where('mobile', $mobile)->first();
        if($data){
            return 1;
        }
        return 0;
    }

    public function newPassword(Request $request){
        $mobile = $request->mobile;
        return view('auth.passwords.confirm')->with(['mobile'=>$mobile]);
    }

    public function newPasswordStore(Request $request){
        $this->validate($request, [
            'mobile' => 'required',
            'password' => 'required',
            'confirmpass' => 'required',
        ]);

        $mobile  = $request->mobile;
        $newPass = $request->password;
        $conPass = $request->confirmpass;

        if($newPass != $conPass){
            session()->flash('error', 'Password does not match.');
            return redirect()->back()->withInput();
        }

        try{
            DB::beginTransaction();

            $data = DB::table('users')->where('mobile', $mobile)->update(['password'=>Hash::make($newPass)]);

            DB::commit();
        }catch(Exception $e){
            DB::rollback();
        }

        if($data){
            session()->flash('success', 'Password change successfully.');
            return redirect()->route('welcome');
        }else{
            session()->flash('error', 'Password does not change.');
            return redirect()->back()->withInput();
        }
    }
}
