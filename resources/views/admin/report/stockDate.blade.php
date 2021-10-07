@extends('admin.layout.app')
@section('maincontent')

<div style="margin-bottom: 1.5rem; margin-top: 1rem;">
   <p style="margin: 0px;padding:0px;">Date: <span style="font-weight: bold;">{{ date("d-m-Y", strtotime($date)) }}</span></p>
   <small>Last Update: {{ date("d-m-Y H:i:s", strtotime($lastUpdate)) }}</small>
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
        @foreach($stockSummary AS $stock)
         <tr>
            <td><a href="{{ route('admin.stock.history', $stock['product_id']) }}" class="text-primary">{{ $stock['product_name'] }}</a></td>
            <td style="text-align: center;">{{ $stock['stockin'] }}</td>
            <td style="text-align: center;">{{ $stock['stockout'] }}</td>
            <td style="text-align: center;">{{ $stock['profit'] }}</td>
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