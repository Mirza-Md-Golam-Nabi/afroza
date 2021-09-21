@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('admin.category.update') }}" method="post" class="mt-3">
    {{ csrf_field() }}
    <input type="hidden" name="category_id" value="{{ $category->id }}">
    <div class="form-group">
        <label for="type_id">Type Name</label>
        <select name="type_id" id="type_id" class="form-control" required>
            <option value="">Please Select One</option>
            @foreach($typeList as $list)
            @if($category->type_id == $list->id)
            <option value="{{ $list->id }}" selected>{{ $list->type_name }}</option>
            @else
            <option value="{{ $list->id }}">{{ $list->type_name }}</option>
            @endif
            @endforeach
        </select> 
    </div>
    <div class="form-group">
      <label for="category_name">Category Name</label> 
      <input type="text" name="category_name" class="form-control" id="category_name" value="{{ $category->category_name }}" required autocomplete="off">
    </div>
    <input type="submit" class="btn btn-primary" value="Update">    
</form>


@endsection

@section('extrascript')

<script>
    // $(document).ready(function(){
    //     $('#type_id').change(function(){
    //         var typeId = $('#type_id').val();
    //         if(typeId != ''){
    //             $.ajax({ 
    //                 url: "{{ route('general.brand.fetch') }}?type-id=" + typeId,
    //                 method: 'GET',
    //                 success: function(data) {
    //                     $('#brand_id').html(data);
    //                 }
    //             });
    //         }
    //     });
    // });
        
</script>

@endsection