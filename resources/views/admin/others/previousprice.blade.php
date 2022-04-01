@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix mt-3">
   <table class="table table-striped table-sm" id="table_id">
      <thead>
      <tr>
          <th scope="col">প্রোডাক্ট নাম</th>
          <th scope="col" class="text-center">P-1</th>
          <th scope="col" class="text-center">P-2</th>
          <th scope="col" class="text-center">P-3</th>
          <th scope="col" class="text-center">P-4</th>
      </tr>
      </thead>
      <tbody>
        @foreach($previousPriceData as $previous)
         <tr>
            <td><a href="{{ route('admin.others.previous.price.id', $previous['product_id']) }}" class="text-primary">{{ $previous['product_name'] }}</a></td>
            <td class="text-center">{{ $previous['price'][0] }}</td>
            <td class="text-center">{{ $previous['price'][1] }}</td>
            <td class="text-center">{{ $previous['price'][2] }}</td>
            <td class="text-center">{{ $previous['price'][3] }}</td>
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
