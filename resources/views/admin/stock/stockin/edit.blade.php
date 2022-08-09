@extends('admin.layout.app')
@section('maincontent')

<!-- Main Content -->

@include('msg')
   <div class="d-flex justify-content-end">
      <a href="{{ route('admin.stockin.date') }}" class="btn btn-primary">Stock-in History</a>
   </div>
   <form action="{{ route('stockins.update', $productId) }}" method="post" class="mt-3">
      @csrf
      @method('PUT')
      <input type="hidden" name="oldDate" value="{{ $date }}">
      <div class="form-group">
         <label for="invoice">Invoice ID <small class="text-muted">(optional)</small></label>
         <input type="text" name="invoice" class="form-control" id="invoice" autocomplete="off" value="">
      </div>
      <div class="form-group">
         <label for="date">Date <small class="text-muted">(required)</small></label>
         <input type="date" name="date" class="form-control" id="date" autocomplete="off" required value="{{ $date }}">
      </div>
      <div class="form-group">
         <label for="">Product</label>
         <div id="product">
            @foreach($stocks as $stock)
            <div id="div{{ $loop->iteration }}">
               <div class="d-flex justify-content-around flex-grow-1 mb-1">
                  <select name="product_id[]" required class="form-control mr-1">
                     <option value="">Please Select One</option>
                     @foreach($products as $product)
                        @if($stock->product_id == $product->id)
                            <option value="{{ $product->id }}" selected>{{ $product->product_name }}</option>
                        @else
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endif
                     @endforeach
                  </select>
                  <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
               </div>
               <div class="d-flex justify-content-around mb-4">
                  <input type="number" name="quantity[]" required class="form-control mr-1" placeholder="Quantity" value="{{ $stock->quantity }}">
                  <input type="number" name="price[]" required class="form-control mr-1" placeholder="Price" value="{{ $stock->buying_price }}">
                  <span class="btn ml-1 btn-danger btn_remove_product" id="{{ $loop->iteration }}">X</span>
               </div>
            </div>
            @endforeach
         </div>
     </div>
      <input type="submit" class="btn btn-primary" value="Update">
   </form>


@endsection

@section('extrascript')

<script>

   $(document).on('click', '.btn_remove_product', function(){
      var button_id = $(this).attr("id");
      $('#div'+button_id).remove();
   });

</script>

@endsection
