@extends('admin.layout.app')
@section('maincontent')

@include('admin.includes.createbutton')

<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr> 
          <th scope="col">S.N</th>
          <th scope="col">Brand Name</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($brandList AS $list)
         <tr>
            <th scope="row">{{ $i++ }}</th>
            <td>{{ $list->brand_name }}</td>
            <td style="text-align: center;"><a href="{{ route('admin.brand.edit', $list->id) }}" class="text-primary">Edit</a></td>
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
         "lengthMenu": [[-1], ["All"]]
       });
   });
 </script>

@endsection