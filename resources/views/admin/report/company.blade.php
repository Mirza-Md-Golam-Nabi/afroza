@extends('admin.layout.app')
@section('maincontent')

<style>
   thead th{
      top: 0%;
      background: #f00;
      z-index: 3;
   }
   thead th:nth-child(1){
      position: sticky;
      left: 0%;
      z-index: 6;
      background: #f00; 
   }
   tbody td:nth-child(1){
      position: sticky;
      left: 0;
      z-index: 3;
      background: #f00; 
   }
</style>

<div>
   Year : <span id="year">Last 12 Month</span>
</div>
<div class="clearfix mt-3">
   <div id="dataShow" style="width: 100%;overflow-x:scroll;">
      <table class="table table-striped table-sm" >
         <thead>
         <tr> 
             <th scope="col">প্রোডাক্ট নাম</th>
             @for($i = 0; $i < 12; $i++)
             <th scope="col" style="text-align: center;">{{ $monthList[$i] }}</th>
             @endfor
         </tr>
         </thead>
         <tbody>
           @foreach($stockSummary AS $stock)
            <tr>
               <td>{{ $stock['product_name'] }}</td>
               @for($i = 1; $i <= 12; $i++)
                  <td style="text-align: center;">{{ $stock[$i] }}</td>
               @endfor
            </tr>
           @endforeach
         </tbody>
      </table>
   </div>
   <select id="yearValue" class="form-control" style="width: auto; display:inline">
      <option value="{{ 10 }}">{{ "Last 12 Month" }}</option>
      @for($i=0; $i < 3; $i++)
      @php $year_value = 2020 + $i; @endphp
      <option value="{{ $year_value }}">{{ $year_value }}</option>
      @endfor
   </select>
   <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#export">Export</button>

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
             <a href="{{ route('admin.export.report.company', ['name'=>$brand,'serial'=>0,'year'=>10]) }}"><button class="btn btn-sm btn-primary">{{ "Last 12 Month" }}</button></a>
             @for($i=0; $i < 3; $i++)
               @php $year_value = 2020 + $i; @endphp
               <a href="{{ route('admin.export.report.company', ['name'=>$brand,'serial'=>1,'year'=>$year_value]) }}"><button class="btn btn-sm btn-primary">{{ $year_value }}</button></a>
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
               }
            });
         }
      });
   });

</script>

@endsection