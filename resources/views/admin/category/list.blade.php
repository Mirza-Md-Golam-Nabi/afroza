@extends('admin.layout.app')
@section('maincontent')

@include('admin.includes.createbutton')
@include('msg')
<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr>
          <th scope="col">S.N</th>
          <th scope="col">Type Name</th>
          <th scope="col">Category Name</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
        @foreach($categories AS $category)
         <tr>
            <th scope="row">{{ $loop->iteration }}</th>
            <td>{{ $category->type->type_name }}</td>
            <td>{{ $category->category_name }}</td>
            <td style="text-align: center;"><a href="{{ route('categories.edit', $category) }}" class="text-primary">Edit</a></td>
         </tr>
        @endforeach
      </tbody>
   </table>
</div>

@endsection

@section('extrascript')

<script type="text/javascript">
   $(document).ready(function(){
      $('#table_id').DataTable({
         "lengthMenu": [[25, 50, -1], [25, 50, "All"]]
       });
   });
 </script>

@endsection
