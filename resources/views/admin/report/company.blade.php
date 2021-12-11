@extends('admin.layout.app')
@section('maincontent')

<style>
   tbody td:nth-child(1){
      position: sticky;
      left: 0;
      z-index: 3;
   }
   tbody td:nth-child(1){
      background-color: #ddd;
   }
</style>

<div>
   Year : <span id="year">Last 12 Month</span>
</div>

@include('admin.includes.stockProfit')

<div class="clearfix mt-3">
   <div id="dataShow" style="width: 100%;overflow-x:scroll;">
      <table class="table table-striped table-sm">
         <thead>
         <tr> 
             <th scope="col">প্রোডাক্ট নাম</th>
             @for($i = 0; $i < 12; $i++)
             <th scope="col" style="text-align: center;">{{ $monthList[$i] }}</th>
             @endfor
             <th scope="col" style="text-align: center;">{{ "Total" }}</th>
         </tr>
         </thead>
         <tbody>
           @foreach($stockSummary AS $stock)
            <tr>
               @php $sum = 0; @endphp
               <td>{{ $stock['product_name'] }}</td>
               @for($i = 1; $i <= 12; $i++)
                  <td style="text-align: center;">{{ $stock[$i] }}</td>
                  @php $sum += $stock[$i]; @endphp
               @endfor
               <th style="text-align: center;">{{ $sum }}</th>
            </tr>
           @endforeach
         </tbody>
         <tfoot>
            <tr>
               <th style="text-align: center;">Total</th>
               @foreach($totalStock AS $stock)
                  <th style="text-align: center;">{{ $stock }}</th>
               @endforeach
            </tr>
         </tfoot>
      </table>
   </div>
   <div class="mt-2">
      <select id="yearValue" class="form-control" style="width: auto; display:inline">
         <option value="{{ 10 }}">{{ "Last 12 Month" }}</option>
         @for($i=2021; $i <= date("Y"); $i++)
         @php $year_value = $i; @endphp
         <option value="{{ $year_value }}">{{ $year_value }}</option>
         @endfor
      </select>
      <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#export">Export</button>
   </div>
   <div class="mt-2">
      
      @foreach(SessionController::brandList() as $brandData)
   @if($brandData->brand_name != $brand)
   <?php 
      $divHead = $brandData->brand_name;
      $url = 'admin.report.company';
      $param = ['name'=>$brandData->brand_name];
   ?>
   <a href="{{ route($url, $param) }}" class="text-primary" style="padding: 5px 5px; margin: 0px 15px;">{{ $divHead }}</a>
   @endif
   @endforeach
   </div>

   <div class="modal fade" id="export" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{{ $brand }} - Export PDF</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
             <a href="{{ route('admin.export.report.company', ['name'=>$brand,'serial'=>0,'year'=>10]) }}"><button class="btn btn-sm btn-primary" onclick="modalDismiss()">{{ "Last 12 Month" }}</button></a>
             @for($i=2021; $i <= date("Y"); $i++)
               @php $year_value = $i; @endphp
               <a href="{{ route('admin.export.report.company', ['name'=>$brand,'serial'=>1,'year'=>$year_value]) }}"><button class="btn btn-sm btn-primary" onclick="modalDismiss()">{{ $year_value }}</button></a>
               @endfor
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection

@section('extrascript')

<script>
   function modalDismiss(){
      $('#export').modal('hide');
   }

   $(document).ready(function(){      
      $('#yearValue').change(function(){
         var name = "{{$brand}}";
         var year = year_value = $('#yearValue').val();
         var serial = 1;
         if(year == 10){
            serial = 0;
            year_value = "Last 12 Month";
         }
         if(year != ''){
            $.ajax({ 
               url: "{{ route('admin.report.company') }}?name="+name+"&serial="+serial+"&year="+year,
               method: 'GET',
               success: function(data) {
                  $('#dataShow').html(data);
                  $('#year').html(year_value);
                  // $('.data').attr("name");
               }
            });
         }
      });

      $('.data').change(function(){
         /** data means 1=stock / 2=profit */
         var data = $(this).attr("value");
         var name = "{{$brand}}";
         var year = $('#year').html();
         var serial = 1;
         if(year == "Last 12 Month"){
            serial = 0;
            year = 10;
            year_value = "Last 12 Month";
         }
         if(data != ''){
            $.ajax({ 
               url: "{{ route('admin.report.ajax') }}?name="+name+"&data="+data+"&serial="+serial+"&year="+year,
               method: 'GET',
               success: function(data) {
                  $('#dataShow').html(data);
               }
            });
         }
      });

   });

</script>

@endsection