@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

<style>
   .contain{
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      grid-gap: 5px;
   }
   .contain-2{
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-gap: 5px;
   }
</style>

<div>
   <h4 style="text-align: center;" class="p-2 bg-success text-white">Profit</h4>
   <div class="contain">
      @for($i=0; $i<count($profit); $i++)
        <div style="text-align: center;">
            {{ date("F",  strtotime( date( 'Y-m-01' )." -$i months")) }}
            <br>
            {{ number_format($profit[$i], 1) }}
        </div>
      @endfor
   </div>
</div>
<hr>

<div>
    <div class="contain-2">
        <div style="text-align: center;">
            Total Product Price
            <br>
            {{ number_format($total_stock_price, 1) }}
        </div>
    </div>
</div>
<hr>
<div class="d-flex align-items-stretch flex-wrap">

   <?php
      $divHead = "Daily Report";
      $url = "admin.report.date";
   ?>
   @include('admin.includes.div')
   <?php
      $divHead = "Weekly Report";
      $url = "admin.report.weekly";
   ?>
   @include('admin.includes.div')
   <?php
      $divHead = "Last 3 Month Report";
      $url = "admin.report.last.3.month";
   ?>
   @include('admin.includes.div')
   <?php
      $divHead = "Monthly Report";
      $url = "admin.report.product.list";
   ?>
   @include('admin.includes.div')
   <?php
      $divHead = "Monthly Profit";
      $url = "admin.report.monthly.profit";
      $param = ['year'=>date('Y')];
   ?>
   @include('admin.includes.divWithParam')
   <?php
      $divHead = "Yearly Report";
      $url = "admin.report.yearly";
   ?>
   @include('admin.includes.div')

   @foreach(SessionController::brandList() as $brand)
   <?php
      $divHead = $brand->brand_name." Report";
      $url = 'admin.report.company';
      $param = ['name'=>$brand->brand_name];
   ?>
   @include('admin.includes.divWithParam')
   @endforeach


</div>
<hr>
<h6 style="text-align: center;" class="p-2 bg-success text-white">Last 30 Days Profit</h6>
<script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = google.visualization.arrayToDataTable([
        ['Date', 'Profit per Day'],
        <?php echo $data['chartData']; ?>
      ]);

      var options = {
          title: 'Profit',
          width: '100%',
          height: 600,
          chartArea: {left:20,top:0,width:'90%',height:'100%'},
          legend: { position: 'none' },
          bars: 'horizontal', // Required for Material Bar Charts.
          axes: {
            x: {
              0: { side: 'top', label: 'Amount (Tk)'} // Top x-axis.
            }
          },
          bar: { groupWidth: "90%" }
        };

      var chart = new google.charts.Bar(document.getElementById('barchart'));

      chart.draw(data, options);
    }
  </script>
<div id="barchart" class="d-flex align-items-stretch flex-wrap"></div>


@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection
