<!-- Modal -->
<div class="modal fade" id="product{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">Product Details</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <style>
        .font{
            font-weight: bold;
        }
       </style>
       <div class="modal-body">
         <p>Type Name : <span class="font">{{ $product->type->type_name }}</span></p>
         <p>Category Name : <span class="font">{{ $product->category->category_name }}</span></p>
         <p>Brand Name : <span class="font">{{ $product->brand->brand_name }}</span></p>
         <p>Product Name : <span class="font">{{ $product->product_name }}</span></p>
         <p>Warning : <span class="font">{{ $product->warning." ".$product->main_unit }}</span></p>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       </div>
     </div>
   </div>
 </div>
