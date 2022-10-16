<?php

namespace App\Http\Controllers;

use App\Model\Product;
use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function test(){
        // $data = [
        //     [
        //         'name' => [
        //             'first'=>'golam', 'second'=>'nabi'
        //         ],
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => [
        //             'first'=>'salam',
        //             'second'=>'hossain'
        //         ],
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => [
        //             'first'=>'kalam',
        //             'second'=>'rahman'
        //         ],
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        // ];

        // $data = [
        //     [
        //         'name' => '1',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '2',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '3',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '4',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '5',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '6',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '7',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '8',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        //     [
        //         'name' => '9',
        //         'city' => 'jessore',
        //         'dist' => 'jessore',
        //     ],
        // ];

// $collect = collect($data)->map(function ($value){
//     return ucfirst($value['name']);
// });

        // return [
        //     'original_data' => $data,
        //     'modified_data' => $collect
        // ];
        // $collection = collect(['name', 'age']);

        // $combined = $collection->combine(['George', 29]);

        // return $combined->all();

$fname=array("Peter","Ben","Joe");
$age=array("35","37","43");

$c = [];
for($i = 0; $i < count($fname); $i++){
    array_push($c, [
        $fname[$i] => $age[$i],
    ]);
}
echo "<pre>";

print_r($c);

return array_combine($fname,$age);


        // echo "</pre>";
        // $chunks = $collection->chunk(3);
        // return $chunks;
        // return view('test')->with(['collection'=>$collection]);
    }
}
