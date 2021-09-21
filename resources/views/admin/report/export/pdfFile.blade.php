<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Afroza Traders</title>
        <style>
           .center{text-align: center; padding: 0; margin: 0;}
           .tableproduct{
                border-collapse: collapse;
                width: 100%;
                font-size:12px;
                line-height:20px;
            }
            .tableproduct tr td, .tableproduct tr th {
               border: 1px solid #dee2e6;                
            }
        </style>
    </head>

   <body>      
        <div class="container">
            <p class="center" style="font-size: 25px;">মেসার্স আফরোজা ট্রেডার্স</p>
            <p class="center">প্রোঃ মির্জা মোঃ ফরহাদ হোসেন</p>
            <p class="center">রূপদিয়া বাজার, যশোর</p>
            <div>
               <p style="margin: auto;">Company Name : {{ $dataList['brand'] }}</p>
               <p style="margin: auto;">Year : {{ $dataList['year'] }}</p>
            </div>
            <table class="tableproduct" style="margin-top: 20px;width:100%;border-collapse: collapse;">
                <thead>
                <tr> 
                    <td style="font-size:18px;">প্রোডাক্ট নাম</td>
                    @for($i = 0; $i < 12; $i++)
                        <td style="text-align: center;">{{ $dataList['monthList'][$i] }}</td>
                    @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataList['stockSummary'] AS $stock){
                        <tr>
                        <td style="font-size:16px;">{{ $stock["product_name"] }}</td>
                        @for($i = 1; $i <= 12; $i++){
                        <td style="text-align: center;">{{ $stock[$i] }}</td>
                        @endfor
                        </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </body>
</html>