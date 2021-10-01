@extends('admin.layout.app')
@section('maincontent')

<div class="d-flex justify-content-between">
   <p style="margin: 0;">Product: <span style="font-weight: bold;">{{ $product->product_name }}</span></p>
   <p style="margin: 0;">Stock: <span style="font-weight:bold;">{{ $product->quantity." ".$product->unit }}</span></p>
</div>
<div style="margin-bottom: 1rem">
   <small>Last Update: {{ $lastUpdate }}</small>
</div>
<div class="clearfix">
   <table class="table table-bordered table-striped table-sm">
      <thead>
      <tr> 
          <th scope="col" style="text-align: center;">তারিখ</th>
          <th scope="col" style="text-align: center;">জমা</th>
          <th scope="col" style="text-align: center;">মোট</th>
          <th scope="col" style="text-align: center;">খরচ</th>
          <th scope="col" style="text-align: center;">মোট</th>
      </tr>
      </thead>
      <tbody>
        @php $i=1; $sum=$product->quantity; @endphp
        @foreach($stockSummary AS $stock)
         <tr>
            <td style="text-align: center;">{{ $stock['date'] }}</td>
            <td style="text-align: center;">{{ $stock['stockin'] }}</td>
            <td style="text-align: center;">{{ $sum + $stock['stockout'] }}</td>
            <td style="text-align: center;">{{ $stock['stockout'] }}</td>
            <td style="text-align: center;">{{ $sum }}</td>
            @php $sum = $sum - $stock['stockin'] + $stock['stockout']; @endphp
         </tr>
        @endforeach
      </tbody>
   </table>
</div>

@endsection

@section('extrascript')


@endsection