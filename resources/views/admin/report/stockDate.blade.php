@extends('admin.layout.app')
@section('maincontent')

<div style="margin-bottom: 1.5rem; margin-top: 1rem;">
   <div class="d-flex justify-content-between">
      <p style="margin: 0px;padding:0px;">Date: <span style="font-weight: bold;">{{ date("d-m-Y", strtotime($date)) }}</span></p>
      <p style="margin: 0px;padding:0px;">Profit: <span style="font-weight: bold;">{{ number_format($profit, 1) }}</span></p>
   </div>
   <small>Update: {{ date("d-m-y H:i:s", strtotime($lastUpdate)) }}</small>
</div>
<div class="clearfix">
   <table class="table table-striped table-sm" id="table_id">
      <thead>
      <tr>
          <th scope="col">প্রোডাক্ট নাম</th>
          <th scope="col" style="text-align: center;">জমা</th>
          <th scope="col" style="text-align: center;">খরচ</th>
          <th scope="col" style="text-align: center;">লাভ</th>
      </tr>
      </thead>
      <tbody>
         @php $in=0; $out=0; @endphp
        @foreach($stockSummary AS $stock)
        @php $in+=$stock['stockin']; $out+=$stock['stockout']; @endphp
         <tr>
            <td><a href="{{ route('admin.stock.history', $stock['product_id']) }}" class="text-primary">{{ $stock['product_name'] }}</a></td>
            <td style="text-align: center;">{{ $stock['stockin'] }}</td>
            <td style="text-align: center;">{{ $stock['stockout'] }}</td>
            <td style="text-align: center;">{{ $stock['profit'] }}</td>
         </tr>
        @endforeach
      </tbody>
      <tfoot>
         <tr>
            <th style="text-align: center">Total</th>
            <th style="text-align: center">{{ $in }}</th>
            <th style="text-align: center">{{ $out }}</th>
            <th style="text-align: center">{{ $profit }}</th>
         </tr>
      </tfoot>
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