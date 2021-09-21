@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr> 
          <th scope="col">Date</th>
          <th scope="col">Product Name</th>
          <th style="text-align:center;" scope="col">Quantity</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
        @foreach($dataList AS $list)
         <tr>
            <td>{{ $list->date }}</td>
            <td>{{ $list->product_name }}</td>
            <td style="text-align:center;">{{ $list->quantity }}</td>
            <td style="text-align: center;"><a href="{{ route('admin.stockout.edit', [$date, $list->product_id]) }}" class="text-primary">Edit</a></td>
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
        "order": [[ 0, "desc" ]],
        "lengthMenu": [[-1], ["All"]]
    });
   });
 </script>

@endsection