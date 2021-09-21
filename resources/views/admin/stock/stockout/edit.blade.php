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
            @php $i=1; @endphp
            @foreach($stockoutList as $stockout)
            <div id="div{{ $i }}">
               <div class="d-flex justify-content-around flex-grow-1 mb-1">  
                  <select name="product_id[]" required class="form-control mr-1">
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
               <div class="d-flex justify-content-around mb-4">
                  <input type="number" name="quantity[]" required class="form-control mr-1" placeholder="Quantity" value="{{ $stockout->quantity }}">    
                  <span class="btn ml-1 btn-danger btn_remove_product" id="{{ $i++ }}">X</span>      
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