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
            @foreach($brands AS $brand)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $brand->brand_name }}</td>
                <td style="text-align: center;"><a href="{{ route('brands.edit', $brand) }}" class="text-primary">Edit</a></td>
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
