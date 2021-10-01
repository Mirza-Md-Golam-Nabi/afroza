@extends('admin.layout.app')
@section('maincontent')

<div style="margin-bottom: 1.5rem; margin-top: 1rem;">
   <p style="margin: 0px;padding:0px;">Date: <span style="font-weight: bold;">{{ $date }}</span></p>
   <small>Last Update: {{ $lastUpdate }}</small>
</div>

<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr>
          <th scope="col">Product Name</th>
          <th style="text-align:center;" scope="col">Quantity</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
         @php $i=0; @endphp
         @foreach($dataList AS $list)
         @php $i++; @endphp
         <tr>
            <td style="cursor: pointer;">{{ $list->product_name }}</td>
            <td style="text-align:center;">{{ $list->quantity }}</td>
            <td style="text-align: center;"><a href="{{ route('admin.stockin.edit', [$date, $list->product_id]) }}" class="text-primary">Edit</a></td>
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
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[-1], ["All"]]
    });
   });
 </script>

@endsection