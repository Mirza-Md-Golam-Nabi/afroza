@extends('admin.layout.app')
@section('maincontent')

<div class="d-flex justify-content-between mb-3">
    <div style="margin-bottom: 1.5rem; margin-top: 1rem;">
        <p style="margin: 0px;padding:0px;">Date:
            <span style="font-weight: bold;">{{ SessionController::date_reverse_full($date) }}</span>
        </p>
        <small>Update: {{ date("d-m-y H:i:s", strtotime($lastUpdate)) }}</small>
    </div>
    <div style="margin-bottom: 1.5rem; margin-top: 1rem;">
        <a href="{{ route('admin.stockin.list', $date) }}">
            <button class="btn btn-success btn-sm">Group</button>
        </a>
        <a href="{{ route('admin.stockin.list.all', $date) }}">
            <button class="btn btn-success btn-sm px-3">All</button>
        </a>
    </div>
</div>

<div class="clearfix">
   <table class="table table-striped" id="table_id">
      <thead>
      <tr>
          <th scope="col">Product Name</th>
          <th style="text-align: center;" scope="col">Quantity</th>
          <th style="text-align: right;" scope="col">Price</th>
          <th style="text-align: center;" scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
         @php $i=0; $total_quantity = 0; @endphp
         @foreach($dataList AS $list)
         @php $i++; @endphp
         <tr>
            <td style="cursor: pointer;">{{ $list->product_name }}</td>
            <td style="text-align: center;">{{ $list->quantity }}</td>
            <td style="text-align: right;">{{ $list->price }}</td>
            <td style="text-align: center;"><a href="{{ route('admin.stockin.edit', [$date, $list->product_id]) }}" class="text-primary">Edit</a></td>
         </tr>
            @php $total_quantity += $list->quantity; @endphp
        @endforeach
      </tbody>
      <tfoot>
        <tr>
            <th>&nbsp;</th>
            <th style="text-align: center;">{{ $total_quantity }}</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
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
