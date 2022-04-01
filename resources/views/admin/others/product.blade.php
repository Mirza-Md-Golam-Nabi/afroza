@extends('admin.layout.app')
@section('maincontent')


<div class="clearfix">
    <table class="table table-striped table-sm" id="table_id">
        <thead>
        <tr>
            <th scope="col" class="text-center">S.N</th>
            <th scope="col" class="text-center">Dateee</th>
            <th scope="col" class="text-center">Quan</th>
            {{-- <th scope="col" class="text-center">Price</th> --}}
            <th scope="col" class="text-center">Per Bag</th>
            <th scope="col" class="text-center">Profit</th>
        </tr>
        </thead>
        <tbody>
            @php $i=1; @endphp
            @foreach($priceList as $price)
            <tr>
                <td class="text-center">{{ $i++ }}</td>
                <td class="text-center">{{ $price->date }}</td>
                <td class="text-center">{{ $price->quantity }}</td>
                {{-- <td class="text-center">{{ $price->price }}</td> --}}
                <td class="text-center">{{ $price->per_bag }}</td>
                <td class="text-center">{{ $price->profit }}</td>
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
