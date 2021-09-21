<!-- Modal -->
<div class="modal fade" id="product{{ $list->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">Product Details</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">
         <p>Type Name : <span style="font-weight: bold;">{{ $list->type_name }}</span></p>
         <p>Category Name : <span style="font-weight: bold;">{{ $list->category_name }}</span></p>
         <p>Brand Name : <span style="font-weight: bold;">{{ $list->brand_name }}</span></p>
         <p>Product Name : <span style="font-weight: bold;">{{ $list->product_name }}</span></p>
         <p>Warning : <span style="font-weight: bold;">{{ $list->warning." ".$list->main_unit }}</span></p>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       </div>
     </div>
   </div>
 </div>