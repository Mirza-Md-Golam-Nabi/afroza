@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
   <small style="color:#f00">সপ্তাহের হিসাবঃ সোমবার থেকে রবিবার</small>
   <table class="table table-striped table-sm" id="table_id">
      <thead>
      <tr> 
          <th scope="col">প্রোডাক্ট নাম</th>
          <th scope="col" style="text-align: center;">{{ "Now" }}</th>
          <th scope="col" style="text-align: center;">{{ "Prev-1" }}</th>
          <th scope="col" style="text-align: center;">{{ "Prev-2" }}</th>
          <th scope="col" style="text-align: center;">{{ "Prev-3" }}</th>
      </tr>
      </thead>
      <tbody>
        @foreach($stockSummary AS $stock)
         <tr>
            <td><a href="{{ route('admin.stock.history', $stock['product_id']) }}" class="text-primary">{{ $stock['product_name'] }}</a></td>
            <td style="text-align: center;">{{ $stock['current'] }} <br>{{ "P=".$stock['profit0'] }}</td>
            <td style="text-align: center;">{{ $stock['prev1'] }} <br>{{ "P=".$stock['profit1'] }}</td>
            <td style="text-align: center;">{{ $stock['prev2'] }} <br>{{ "P=".$stock['profit2'] }}</td>
            <td style="text-align: center;">{{ $stock['prev3'] }} <br>{{ "P=".$stock['profit3'] }}</td>
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