@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr> 
          <th scope="col">S.N</th>
          <th scope="col">Product Name</th>
          <th style="text-align: center;" scope="col">Quantity</th>
      </tr>
      </thead>
      <tbody>
        @php $i=1; @endphp
        @foreach($stockList AS $list)
         <tr>
            <th scope="row">{{ $i++ }}</th>
            <td><a href="{{ route('admin.stock.history', $list->product_id) }}" class="text-primary">{{ $list->product_name }}</a></td>
            <td style="text-align: center;">{{ $list->quantity }}</td>
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