<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Product;
use Auth;
use DB;


class FrontendController extends Controller
{
    public function index(){ 
        return view('welcome');
    }
}
