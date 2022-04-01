@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
   <table class="table table-striped table-sm" id="table_id">
      <thead>
      <tr>
          <th scope="col">প্রোডাক্ট নাম</th>
          <th scope="col" style="text-align: center;">Current</th>
          <th scope="col" style="text-align: center;">Upcoming</th>
      </tr>
      </thead>
      <tbody>
        @foreach($data['stockSummary'] AS $stock)
         <tr>
            <td><a href="{{ route('admin.stock.history', $stock['product_id']) }}" class="text-primary">{{ $stock['product_name'] }}</a></td>
            <td style="text-align: center;">
               ৳ = {{ $stock['current']['price'] }}<br>
               Qnty: {{ $stock['current']['quantity'] }}<br>
               লাভঃ {{ $stock['current']['profit'] }}
            </td>
            <td style="text-align: center;">
               @if(count($stock['upcoming']) > 0)
               @php $total=count($stock['upcoming']); $count=0; @endphp
                  @foreach($stock['upcoming'] as $upcoming)
                  @php $count++; @endphp
                     ৳ = {{ $upcoming['price'] }}<br>
                     Qnty: {{ $upcoming['quantity'] }}
                     @if($total != $count)<br><br>@endif
                  @endforeach
               @else
                  {{ 0 }}
               @endif
            </td>
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
         "lengthMenu": [[-1], ["All"]]
       });
   });
 </script>

@endsection
