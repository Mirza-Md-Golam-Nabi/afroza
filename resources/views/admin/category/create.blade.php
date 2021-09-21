@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg') 

<form action="{{ route('admin.category.store') }}" method="post" class="mt-3">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="type_id">Type Name</label>
        <select name="type_id" id="type_id" class="form-control" required autofocus>
            <option value="">Please Select One</option>
            @foreach($typeList as $list)
            <option value="{{ $list->id }}">{{ $list->type_name }}</option>
            @endforeach
        </select> 
    </div>
    <div class="form-group">
      <label for="category_name">Category Name</label> 
      <input type="text" name="category_name" class="form-control" id="category_name" autocomplete="off">
    </div>
    <input type="submit" class="btn btn-primary" value="Submit">    
</form>

@endsection

@section('extrascript')


@endsection