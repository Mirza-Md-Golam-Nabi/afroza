@extends('admin.layout.app')
@section('maincontent')

@include('admin.includes.createbutton')

@include('msg')

<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr> 
          <th scope="col">S.N</th>
          <th scope="col">Product Name</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($productList AS $list)
        <?php $check = $list->status ? 0 : 1; ?>
         <tr>
            <th scope="row">{{ $i++ }}</th>
            <td data-toggle="modal" data-target="#product{{ $list->id }}" style="cursor: pointer;"><b>{{ $list->product_name }}</b></td>
            <td style="text-align:center"> 
               <a class="text-primary" href="{{ route('admin.product.edit', $list->id) }}">Edit</a> ||               
               <a class="@if($list->status == 1){{ 'text-danger' }} @else {{ 'text-success' }} @endif " href="{{ route('admin.product.status', [$list->id, 'status'=>$check ]) }}">
                  @if($list->status == 1) {{ 'Inactive' }} @else {{ 'Active' }} @endif
               </a>
               
            </td>
         </tr>

         @include('admin.includes.modalProduct')
 
         @endforeach



      </tbody>
   </table>
</div>

@endsection

@section('extrascript')

<script type="text/javascript">
   $(document).ready(function(){
      $('#table_id').DataTable({
         "lengthMenu": [[50, 75, -1], [50, 75, "All"]]
       });
   });

   

 </script>

@endsection

