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
        @foreach($products AS $product)
         <tr>
            <th scope="row">{{ $loop->iteration }}</th>
            <td data-toggle="modal" data-target="#product{{ $product->id }}" style="cursor: pointer;"><b>{{ $product->product_name }}</b></td>
            <td style="text-align:center">
               <a class="text-primary" href="{{ route('products.edit', $product) }}">Edit</a> ||
               <a class="@if($product->status){{ 'text-danger' }} @else {{ 'text-success' }} @endif " href="{{ route('admin.product.status', ['id'=>$product->id, 'status'=>!$product->status ]) }}">
                  @if($product->status) {{ 'Inactive' }} @else {{ 'Active' }} @endif
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

