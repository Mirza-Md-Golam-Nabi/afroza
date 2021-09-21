@extends('admin.layout.app')
@section('maincontent')

<div>
   *** <small style="color:#f00;">শেষ ১২ মাসের রিপোর্ট</small> ***
</div>
<div class="d-flex justify-content-between">
   <p>Product: <span style="font-weight: bold;">{{ $product->product_name }}</span></p>
   <p>Stock: <span style="font-weight:bold;">{{ $product->quantity." বস্তা" }}</span></p>
</div>
<div class="clearfix">
   <table class="table table-striped table-sm">
      <thead>
      <tr> 
          <th scope="col">Month</th>
          <th scope="col" style="text-align: center;">Quantity</th>
          <th scope="col">Month</th>
          <th scope="col" style="text-align: center;">Quantity</th>
      </tr>
      </thead>
      <tbody>
         @php $count = 0; @endphp
        @foreach($stockSummary AS $stock)
        @if($count == 0)
         <tr>
            <td>{{ $stock['month'] }}</td>
            <td style="text-align: center;">{{ $stock['quantity']." বস্তা" }}</td>
         @php $count = 1; @endphp
         @elseif($count == 1)
            <td>{{ $stock['month'] }}</td>
            <td style="text-align: center;">{{ $stock['quantity']." বস্তা" }}</td>
         </tr>
         @php $count = 0; @endphp
         @endif
        @endforeach
      </tbody>
   </table>
</div>
<div class="d-flex justify-content-end">
   <a href="{{ route('admin.stock.history', $product->product_id) }}"><i class="text-primary">Details</i></a>
</div>

@endsection

@section('extrascript')


@endsection