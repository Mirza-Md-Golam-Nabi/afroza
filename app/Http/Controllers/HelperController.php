<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function __construct(){
        date_default_timezone_set('Asia/Dhaka');
    }
}
