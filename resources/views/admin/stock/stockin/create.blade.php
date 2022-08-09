@extends('admin.layout.app')
@section('maincontent')

<!-- Main Content -->

@include('msg')

   <form action="{{ route('stockins.store') }}" method="post" class="mt-3">
      @csrf
      <div class="form-group">
         <label for="invoice">Invoice ID <small class="text-muted">(optional)</small></label>
         <input type="text" name="invoice" class="form-control" id="invoice" autocomplete="off">
      </div>
      <div class="form-group">
         <label for="date">Date <small class="text-muted">(required)</small></label>
         <input type="date" name="date" class="form-control" id="date" autocomplete="off" required>
      </div>
      <div class="form-group">
         <label for="">Product</label>
         <div id="product">
            <div class="d-flex justify-content-around flex-grow-1 mb-1">
               <select name="product_id[]" data-product="1" required class="form-control mr-1 target_product">
                  <option value="">Please Select One</option>
                  @foreach($products as $product)
                     <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                  @endforeach
               </select>
               <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="d-flex justify-content-around mb-1">
               <input type="number" name="quantity[]" required class="form-control mr-1" placeholder="Quantity">
               <input type="number" name="price[]" required class="form-control mr-1" placeholder="Price">
               <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
            </div>
         </div>
         <span class="btn btn-success btn-sm mt-2" id="addmore">Add More</span>
     </div>
      <input type="submit" class="btn btn-primary" value="Submit">
   </form>


@endsection

@section('extrascript')

<script>
   $(document).ready(function(){
      var i = 1;
      $('#addmore').click(function(){
         i++;
         var data = `<div id="div${i}" class="mt-3">
                  <div class="d-flex justify-content-around flex-grow-1 mb-1" id=>
                     <select name="product_id[]" data-product="${i}" required class="form-control mr-1 target_product">
                        <option value="">Please Select One</option>
                        @foreach($products as $product)
                           <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                     </select>
                     <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
                  </div>
                  <div class="d-flex justify-content-around mb-1">
                     <input type="number" name="quantity[]" required class="form-control mr-1" placeholder="Quantity">
                     <input type="number" name="price[]" required class="form-control mr-1" placeholder="Price">
                     <span class="btn ml-1 btn-danger btn_remove_product" id="${i}">X</span>
                  </div>
               </div>`;
         $('#product').append(data);
      });
   });

   $(document).on('click', '.btn_remove_product', function(){
        var button_id = $(this).attr("id");
        $('#div'+button_id).remove();
    });

</script>

@endsection
