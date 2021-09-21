@extends('admin.layout.app')
@section('maincontent')

@include('admin.includes.createbutton')

<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr> 
          <th scope="col">S.N</th>
          <th scope="col">Type Name</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($typeList AS $list)
         <tr>
            <th scope="row">{{ $i++ }}</th>
            <td>{{ $list->type_name }}</td>
            <td style="text-align: center;"><a href="{{ route('admin.type.edit', $list->id) }}" class="text-primary">Edit</a></td>
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