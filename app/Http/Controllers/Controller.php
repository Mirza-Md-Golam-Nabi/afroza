<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    public $newProduct = 1;
    public $oldProduct = 2;
    public $productFetch = 8;
    public $admin_contact_no = "01711977107";
    
    public function __construct(){
        date_default_timezone_set('Asia/Dhaka');
    }
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
