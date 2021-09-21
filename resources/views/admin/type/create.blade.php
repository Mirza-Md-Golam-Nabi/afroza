@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

    <form action="{{ route('admin.type.store') }}" method="post" class="mt-3">
        {{ csrf_field() }}
        <div class="form-group">
          <label for="typeName">Type Name</label>
          <input type="text" name="typeName" class="form-control" id="typeName" autocomplete="off" autofocus>
        </div>
        <input type="submit" class="btn btn-primary" value="Submit">    
    </form>



@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection