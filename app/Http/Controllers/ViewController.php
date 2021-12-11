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

    public static function companyStock($request_data){
        $monthList = SessionController::monthList($request_data['serial']);
        $data = SessionController::dataFetch($request_data);

        $output = '';
        $output .= '<table class="table table-striped table-sm">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>';
            for($i = 0; $i < 12; $i++){
                $output .= '<th scope="col" style="text-align: center;">'.$monthList[$i].'</th>';
            }
            $output .= '<th scope="col" style="text-align: center;">Total</th>';          
            $output .= '</tr>
        </thead>
        <tbody>';
            foreach($data['stock'] AS $stock){
                $sum = 0;
            $output .= '<tr>
                <td>'.$stock['product_name'].'</td>';
                for($i = 1; $i <= 12; $i++){
                    $output .= '<td style="text-align: center;">'.$stock[$i].'</td>';
                    $sum += $stock[$i];
                }
                $output .= '<th style="text-align: center;">'.$sum.'</th>';
                $output .= '</tr>';
            }
            $output .= '</tbody><tfoot><tr>';
            $output .= '<th style="text-align: center">Total</th>';
            for($i = 1; $i <= 12; $i++){
                $output .= '<th style="text-align: center;">'.$data['totalStock'][$i].'</th>';
            }
        $output .= '</tr></tfoot></table>';

        return $output;
    }

    public static function companyProfit($request_data){
        $monthList = SessionController::monthList($request_data['serial']);
        $data = SessionController::dataFetch($request_data);
        
        $output = '';
        $output .= '<table class="table table-striped table-sm">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>';
            for($i = 0; $i < 12; $i++){
                $output .= '<th scope="col" style="text-align: center;">'.$monthList[$i].'</th>';
            }
            $output .= '<th scope="col" style="text-align: center;">Total</th>';
            $output .= '</tr>
        </thead>
        <tbody>';
            foreach($data['profit'] AS $stock){
                $sum = 0;
            $output .= '<tr>
                <td>'.$stock['product_name'].'</td>';
                for($i = 1; $i <= 12; $i++){
                    $output .= '<td style="text-align: center;">'.$stock[$i].'</td>';
                    $sum += $stock[$i];
                }
                $output .= '<th style="text-align: center;">'.$sum.'</th>';
                $output .= '</tr>';
            }
            $output .= '</tbody><tfoot><tr>';
            $output .= '<th style="text-align: center">Total</th>';
            for($i = 1; $i <= 12; $i++){
                $output .= '<td style="text-align: center; font-weight:bold;">'.$data['totalProfit'][$i].'</td>';
            }
        $output .= '</tr></tfoot></table>';

        return $output;
    }
}
