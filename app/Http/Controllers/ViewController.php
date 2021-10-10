<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public static function weeklyStock(){
        $data = SessionController::weekly();
        $output = '';
        $output .= '<table class="table table-striped table-sm" id="table_id">
         <thead>
         <tr> 
             <th scope="col">প্রোডাক্ট নাম</th>
             <th scope="col" style="text-align: center;">Now</th>
             <th scope="col" style="text-align: center;">P-1</th>
             <th scope="col" style="text-align: center;">P-2</th>
             <th scope="col" style="text-align: center;">P-3</th>
         </tr>
         </thead>
         <tbody>';
            foreach($data['stockSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
               <td style="text-align: center;">'.$stock['current'].'</td>
               <td style="text-align: center;">'.$stock['prev1'].'</td>
               <td style="text-align: center;">'.$stock['prev2'].'</td>
               <td style="text-align: center;">'.$stock['prev3'].'</td>
            </tr>';
        }
        $output .= '</tbody>
            </table>';

        return $output;
    }

    public static function weeklyProfit(){
        $data = SessionController::weekly();
        $output = '';
        $output .= '<table class="table table-striped table-sm" id="table_id">
         <thead>
         <tr> 
             <th scope="col">প্রোডাক্ট নাম</th>
             <th scope="col" style="text-align: center;">Now</th>
             <th scope="col" style="text-align: center;">P-1</th>
             <th scope="col" style="text-align: center;">P-2</th>
             <th scope="col" style="text-align: center;">P-3</th>
         </tr>
         </thead>
         <tbody>';
            foreach($data['profitSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
               <td style="text-align: right;">'.$stock['profit0'].'</td>
               <td style="text-align: right;">'.$stock['profit1'].'</td>
               <td style="text-align: right;">'.$stock['profit2'].'</td>
               <td style="text-align: right;">'.$stock['profit3'].'</td>
            </tr>';
        }
        $output .= '</tbody>
        <tfoot>
            <tr>
            <th style="text-align:right">Profit:</th>';
            foreach($data['profit'] as $prof){
                $output .= '<th style="text-align:right">'.number_format($prof, 1).'</th>';
        }
        $output .= '</tr>
        </tfoot>
            </table>';

        return $output;
    }

    public static function last3MonthStock(){
        $data = SessionController::last3Month();
        $output = '';
        $output .= '<table class="table table-striped table-sm" id="table_id">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>
            <th scope="col" style="text-align: center;">'.date('M').'</th>
            <th scope="col" style="text-align: center;">'.date('M', strtotime('-1 month')).'</th>
            <th scope="col" style="text-align: center;">'.date('M', strtotime('-2 month')).'</th>
        </tr>
        </thead>
        <tbody>';
          foreach($data['stockSummary'] AS $stock){
            $output .= '<tr>
              <td><a href="'.route('admin.stock.history', $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
              <td style="text-align: center;">'.$stock['current_out'].'</td>
              <td style="text-align: center;">'.$stock['prev1_out'].'</td>
              <td style="text-align: center;">'.$stock['prev2_out'].'</td>
           </tr>';
        }
        $output .= '</tbody>
        </table>';

        return $output;
    }

    public static function last3MonthProfit(){
        $data = SessionController::last3Month();
        $output = '';
        $output .= '<table class="table table-striped table-sm" id="table_id">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>
            <th scope="col" style="text-align: center;">'.date('M').'</th>
            <th scope="col" style="text-align: center;">'.date('M', strtotime('-1 month')).'</th>
            <th scope="col" style="text-align: center;">'.date('M', strtotime('-2 month')).'</th>
        </tr>
        </thead>
        <tbody>';
          foreach($data['profitSummary'] AS $stock){
            $output .= '<tr>
              <td><a href="'.route('admin.stock.history', $stock['product_id']).'" class="text-primary">'.$stock['product_name'].'</a></td>
              <td style="text-align: right;">'.$stock['profit0'].'</td>
              <td style="text-align: right;">'.$stock['profit1'].'</td>
              <td style="text-align: right;">'.$stock['profit2'].'</td>
           </tr>';
        }
        $output .= '</tbody>
        <tfoot>
           <tr>
              <th style="text-align:right">Profit:</th>';
              foreach($data['profit'] as $prof){
                $output .= '<th style="text-align:right">'.number_format($prof, 1).'</th>';
            }
            $output .= '</tr>
        </tfoot>
     </table>';

        return $output;
    }

    public static function yearlyStock(){
        $data = SessionController::yearly();
        $output = '';
        $output .= '<table class="table table-striped table-sm" id="table_id">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>
            <th scope="col" style="text-align: center;">'.date("Y").'</th>
            <th scope="col" style="text-align: center;">'.date("Y", strtotime("-1 year")).'</th>
            <th scope="col" style="text-align: center;">'.date("Y", strtotime("-2 year")).'</th>
        </tr>
        </thead>
        <tbody>';
            foreach($data['stockSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock["product_id"]).'" class="text-primary">'.$stock["product_name"].'</a></td>
               <td style="text-align: center;">'.$stock["current_out"].'</td>
               <td style="text-align: center;">'.$stock["prev1_out"].'</td>
               <td style="text-align: center;">'.$stock["prev2_out"].'</td>
            </tr>';
            }
            $output .= '</tbody>
        </table>';
      
        return $output;
    }

    public static function yearlyProfit(){
        $data = SessionController::yearly();
        $output = '';
        $output .= '<table class="table table-striped table-sm" id="table_id">
        <thead>
        <tr> 
            <th scope="col">প্রোডাক্ট নাম</th>
            <th scope="col" style="text-align: center;">'.date("Y").'</th>
            <th scope="col" style="text-align: center;">'.date("Y", strtotime("-1 year")).'</th>
            <th scope="col" style="text-align: center;">'.date("Y", strtotime("-2 year")).'</th>
        </tr>
        </thead>
        <tbody>';
            foreach($data['profitSummary'] AS $stock){
            $output .= '<tr>
               <td><a href="'.route("admin.stock.history", $stock["product_id"]).'" class="text-primary">'.$stock["product_name"].'</a></td>
               <td style="text-align: center;">'.$stock["profit0"].'</td>
               <td style="text-align: center;">'.$stock["profit1"].'</td>
               <td style="text-align: center;">'.$stock["profit2"].'</td>
            </tr>';
            }
            $output .= '</tbody>
            <tfoot>';
                $output .= '<th style="text-align: right;">Profit</th>';
            foreach($data['profit'] as $profit){
                $output .= '<th style="text-align: center;">'.$profit.'</th>';
            }
            $output .= '</tfoot>
        </table>';
      
        return $output;
    }
}
