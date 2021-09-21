@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
   <table class="table table-striped table-sm" id="table_id">
      <thead>
      <tr> 
          <th scope="col">প্রোডাক্ট নাম</th>
          <th scope="col" style="text-align: center;">{{ date('M') }}</th>
          <th scope="col" style="text-align: center;">{{ date('M', strtotime('-1 month')) }}</th>
          <th scope="col" style="text-align: center;">{{ date('M', strtotime('-2 month')) }}</th>
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

@endsection

@section('extrascript')

<script type="text/javascript">
   $(document).ready(function(){
       $('#table_id').DataTable({
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[-1], ["All"]]
    });
   });
</script>

@endsection