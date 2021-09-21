<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Model\Brand;
use DB;

class ExportController extends Controller
{
    public function filterByDow($stockSummary, $dow){
        return array_filter($stockSummary, function($item) use ($dow) {
            if($item['product_id'] == $dow){
                return true;
            }
        });
    }

    public function companyReport(Request $request){
        $company_name = $request->get('name');
        $serial_month = $request->get('serial');        
        $year = $request->get('year') ? $request->get('year') : $year = date('Y');
        $year_num = $year;
        if($year == 10){
            $year_num = "Last 12 month";
        }

        $brand = Brand::select('id', 'brand_name')->where('brand_name', $company_name)->first();
        $title = $brand->brand_name." Report";

        $serial = 0;
        if($serial_month){
            $serial = 1;
        }

        $request_data = [
            'brand' => $brand->id,
            'serial' => $serial,
            'year' => $year
        ];

        $monthList = SessionController::monthList($serial);
        $stockSummary = SessionController::dataFetch($request_data);

        $dataList = [
            'stockSummary'  => $stockSummary, 
            'monthList'     => $monthList,
            'brand'         => $brand->brand_name,
            'year'          => $year_num
        ];

        $pdf = $this->pdfCreate($dataList);
        return redirect()->route('admin.export.report.company');
    }

    public function pdfCreate($dataList){
        
        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 12,
	        'default_font' => 'nikosh'
        ]);
        
        // Write some HTML code:
        $mpdf->WriteHTML($this->pdfHTML($dataList));        

        // Output a PDF file directly to the browser        
        
        $pdfFileName = $dataList['brand']."-".$dataList['year'].".pdf";
        
        // $mpdf->Output('uploads/pdf/'.$pdfFileName,'F');
        $mpdf->Output($pdfFileName,'D');
        
        return 1;        
    }

    public function pdfHTML($dataList){        
        return view('admin.report.export.pdfFile')->with(['dataList'=>$dataList]);
    }
}
