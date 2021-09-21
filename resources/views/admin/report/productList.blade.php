@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr> 
          <th scope="col">S.N</th>
          <th scope="col">Product Name</th>
      </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($productList AS $list)
         <tr>
            <th scope="row">{{ $i++ }}</th>
            <td><a href="{{ route('admin.report.monthly', $list->product_id) }}" class="text-primary">{{ $list->product_name }}</a></td>
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