<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public static function weeklyStock(){
        $data = SessionController::weekly();
        $output = '';
        foreach($data['stockSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
               <td style="text-align: center;">'.$stock['current'].'</td>
               <td style="text-align: center;">'.$stock['prev1'].'</td>
               <td style="text-align: center;">'.$stock['prev2'].'</td>
               <td style="text-align: center;">'.$stock['prev3'].'</td>
            </tr>';
        }

        return $output;
    }

    public static function weeklyProfit(){
        $data = SessionController::weekly();
        $output = '';
        foreach($data['profitSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
               <td style="text-align: right;">'.$stock['profit0'].'</td>
               <td style="text-align: right;">'.$stock['profit1'].'</td>
               <td style="text-align: right;">'.$stock['profit2'].'</td>
               <td style="text-align: right;">'.$stock['profit3'].'</td>
            </tr>';
        }

        return $output;
    }

    public static function last3MonthStock(){
        $data = SessionController::last3Month();
        $output = '';
        foreach($data['stockSummary'] AS $stock){
            $output .= '<tr>
              <td><a href="'.route('admin.stock.history', $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
              <td style="text-align: center;">'.$stock['current_out'].'</td>
              <td style="text-align: center;">'.$stock['prev1_out'].'</td>
              <td style="text-align: center;">'.$stock['prev2_out'].'</td>
           </tr>';
        }

        return $output;
    }

    public static function last3MonthProfit(){
        $data = SessionController::last3Month();
        $output = '';
        foreach($data['profitSummary'] AS $stock){
            $output .= '<tr>
              <td><a href="'.route('admin.stock.history', $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
              <td style="text-align: right;">'.$stock['profit0'].'</td>
              <td style="text-align: right;">'.$stock['profit1'].'</td>
              <td style="text-align: right;">'.$stock['profit2'].'</td>
           </tr>';
        }

        return $output;
    }

    public static function yearlyStock(){
        $data = SessionController::yearly();
        $output = '';
        foreach($data['stockSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock["product_id"]).'" class="text-primary">'.$stock["product_name"].'</a></td>
               <td style="text-align: center;">'.$stock["current_out"].'</td>
               <td style="text-align: center;">'.$stock["prev1_out"].'</td>
               <td style="text-align: center;">'.$stock["prev2_out"].'</td>
            </tr>';
        }
      
        return $output;
    }

    public static function yearlyProfit(){
        $data = SessionController::yearly();
        $output = '';
        foreach($data['profitSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock["product_id"]).'" class="text-primary">'.$stock["product_name"].'</a></td>
               <td style="text-align: center;">'.$stock["profit0"].'</td>
               <td style="text-align: center;">'.$stock["profit1"].'</td>
               <td style="text-align: center;">'.$stock["profit2"].'</td>
            </tr>';
        }
      
        return $output;
    }
}
