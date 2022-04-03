@extends('admin.layout.app')
@section('maincontent')

<!-- Main Content -->

@include('msg')

   <form action="{{ route('admin.stockout.store') }}" method="post" class="mt-3">
      {{ csrf_field() }}
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
                  @foreach($productList as $list)
                     <option value="{{ $list->id }}">{{ $list->product_name }}</option>
                  @endforeach
               </select>
               <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div class="d-flex justify-content-around mb-1">
               <input type="number" name="quantity[]" data-quantity="1" required class="form-control mr-1 quantity" placeholder="Quantity">
               <input type="number" name="price[]" required class="form-control mr-1" placeholder="Price">
               <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div><small id="msg1" style="color:#f00;"></small></div>
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
                        @foreach($productList as $list)
                           <option value="{{ $list->id }}">{{ $list->product_name }}</option>
                        @endforeach
                     </select>
                     <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
                  </div>
                  <div class="d-flex justify-content-around mb-1">
                     <input type="number" name="quantity[]" required data-quantity="${i}" class="form-control mr-1 quantity" placeholder="Quantity">
                     <input type="number" name="price[]" required class="form-control mr-1" placeholder="Price">
                     <span class="btn ml-1 btn-danger btn_remove_product" id="${i}">X</span>
                  </div>
                  <div><small id="msg${i}" style="color:#f00;"></small></div>
               </div>`;
         $('#product').append(data);
      });
   });

   $(document).on('click', '.btn_remove_product', function(){
        var button_id = $(this).attr("id");
        $('#div'+button_id).remove();
    });

    $(document).on('keyup', '.quantity', function(){
        var dataQuantityId = $(this).data("quantity");
        var dataQuantityValue = parseInt($(this).val());
        var prodId = document.querySelector("[data-product='"+dataQuantityId+"']").value;
        if(dataQuantityValue){
            $.ajax({
                url: "{{ route('general.stock.check') }}?productID=" + prodId,
                method: 'GET',
                success: function(data) {
                   if(dataQuantityValue > data.quantity){
                     $('#msg'+dataQuantityId).html("Available Quantity = " + data.quantity);
                   }else if(data.price == 0){
                     $('#msg'+dataQuantityId).html("Buying price is not set");
                   }else{
                        if(!dataQuantityValue){
                            $('#msg'+dataQuantityId).html("Product Price = " + data.price);
                        }else{
                            $('#msg'+dataQuantityId).html("Product Price = " + (data.price * dataQuantityValue));
                        }
                   }
                  }
            });
        }else{
         $('#msg'+dataQuantityId).html("");
        }
    });

   $(document).on('change', '.target_product', function(){
      var dataProductId = $(this).data("product");
      var dataProductValue = parseInt($(this).val());
      var dataQuantityValue = document.querySelector("[data-quantity='"+dataProductId+"']").value;
      if(dataProductValue){
         $.ajax({
            url: "{{ route('general.stock.check') }}?productID=" + dataProductValue,
            method: 'GET',
            success: function(data) {
               if(dataQuantityValue > data.quantity){
                  $('#msg'+dataProductId).html("Available Quantity = " + data.quantity);
               }else if(data.price == 0){
                  $('#msg'+dataProductId).html("Buying price is not set");
               }else{
                   if(!dataQuantityValue){
                        $('#msg'+dataProductId).html("Product Price = " + data.price);
                   }else{
                        $('#msg'+dataProductId).html("Product Price = " + (data.price * dataQuantityValue));
                   }
               }
            }
         });
      }else{
         $('#msg'+dataProductId).html("");
      }
   });

</script>`

@endsection
