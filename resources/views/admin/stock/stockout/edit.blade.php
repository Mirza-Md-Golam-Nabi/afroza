@extends('admin.layout.app')
@section('maincontent')

<!-- Main Content -->

@include('msg')
   <div class="d-flex justify-content-end">
      <a href="{{ route('admin.stockout.date') }}" class="btn btn-primary">Stock-out History</a>
   </div>
   <form action="{{ route('admin.stockout.update') }}" method="post" class="mt-3">
      {{ csrf_field() }}
      <input type="hidden" name="oldProduct_id" value="{{ $product_id }}">
      <input type="hidden" name="oldDate" value="{{ $date }}">
      <div class="form-group">
         <label for="date">Date <small class="text-muted">(required)</small></label>
         <input type="date" name="date" class="form-control" id="date" autocomplete="off" required value="{{ $date }}">
      </div>
      <div class="form-group">
         <label for="">Product</label>
         <div id="product">
            @php $i=0; @endphp
            @foreach($stockoutList as $stockout)
            @php $i++; @endphp
            <div id="div{{ $i }}">
               <div class="d-flex justify-content-around flex-grow-1 mb-1">
                  <select name="product_id[]" data-product="{{$i}}" required class="form-control mr-1 target_product">
                     <option value="">Please Select One</option>
                     @foreach($productList as $list)
                        @if($stockout->product_id == $list->id)
                        <option value="{{ $list->id }}" selected>{{ $list->product_name }}</option>
                        @else
                        <option value="{{ $list->id }}">{{ $list->product_name }}</option>
                        @endif
                     @endforeach
                  </select>
                  <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
               </div>
               <div class="d-flex justify-content-around">
                  <input type="number" name="quantity[]" data-quantity="{{$i}}" required class="form-control mr-1 quantity" placeholder="Quantity" value="{{ $stockout->quantity }}">
                  <input type="number" name="price[]" required class="form-control mr-1" placeholder="Price" value={{ $stockout->selling_price}}>
                  <span class="btn ml-1 btn-danger btn_remove_product" id="{{ $i }}">X</span>
               </div>
               <div class=" mb-4"><small id="msg{{$i}}" style="color:#f00;"></small></div>
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
      $('#msg'+button_id).remove();
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
                            $('#msg'+dataQuantityId).html(data.product_name + " = " + data.price);
                        }else{
                            $('#msg'+dataQuantityId).html(data.product_name + " = " + (data.price * dataQuantityValue));
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
                            $('#msg'+dataQuantityId).html(data.product_name + " = " + data.price);
                        }else{
                            $('#msg'+dataQuantityId).html(data.product_name + " = " + (data.price * dataQuantityValue));
                        }
                   }
            }
         });
      }else{
         $('#msg'+dataProductId).html("");
      }
   });

</script>

@endsection
