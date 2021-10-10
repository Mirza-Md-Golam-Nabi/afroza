@extends('admin.layout.app')
@section('maincontent')

@include('admin.includes.stockProfit')

<div class="clearfix">
   <div id="dataShow">
      <table class="table table-striped table-sm" id="table_id">
         <thead>
         <tr> 
             <th scope="col">প্রোডাক্ট নাম</th>
             <th scope="col" style="text-align: center;">{{ date('Y') }}</th>
             <th scope="col" style="text-align: center;">{{ date('Y', strtotime('-1 year')) }}</th>
             <th scope="col" style="text-align: center;">{{ date('Y', strtotime('-2 year')) }}</th>
         </tr>
         </thead>
         <tbody>
           @foreach($stockSummary AS $stock)
            <tr>
               <td><a href="{{ route('admin.stock.history', $stock['product_id']) }}" class="text-primary">{{ $stock['product_name'] }}</a></td>
               <td style="text-align: center;">{{ $stock['current_out'] }}</td>
               <td style="text-align: center;">{{ $stock['prev1_out'] }}</td>
               <td style="text-align: center;">{{ $stock['prev2_out'] }}</td>
            </tr>
           @endforeach
         </tbody>
      </table>
   </div>
</div>

@endsection

@section('extrascript')

<script type="text/javascript">
   $(document).ready(function(){
      $('#table_id').DataTable({
         "order": [[ 0, "asc" ]],
         "lengthMenu": [[-1], ["All"]]
      });

      $('.data').change(function(){
         /** data means 1=stock / 2=profit */
         var data = $(this).attr("value");
         /** name means 1=weekly / 2=monthly / 3=yearly */
         var name = 3;
         if(data != ''){
            $.ajax({ 
               url: "{{ route('admin.report.ajax') }}?name="+name+"&data="+data,
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