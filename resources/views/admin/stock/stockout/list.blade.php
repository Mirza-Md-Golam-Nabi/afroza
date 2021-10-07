@extends('admin.layout.app')
@section('maincontent')

<div style="margin-bottom: 1.5rem; margin-top: 1rem;">
   <p style="margin: 0px;padding:0px;">Date: <span style="font-weight: bold;">{{ date("d-m-Y", strtotime($date)) }}</span></p>
   <small>Last Update: {{ date("d-m-Y H:i:s", strtotime($lastUpdate)) }}</small>
</div>

<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr>
          <th scope="col">Product Name</th>
          <th style="text-align:center;" scope="col">Quantity</th>
          <th style="text-align:center;" scope="col">Details</th>
          <th style="text-align:center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
        @foreach($dataList AS $list)
         <tr>
            <td>{{ $list->product_name }}</td>
            <td style="text-align:center;">{{ $list->quantity }}</td>
            <td style="text-align:left;">
               {{ "S=".$list->sell }}<br>
               {{ "B=".$list->buy }}<br>
               {{ "P=".($list->sell - $list->buy) }}
            </td>
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
        "order": [[ 0, "asc" ]],
        "lengthMenu": [[-1], ["All"]]
    });
   });
 </script>

@endsection