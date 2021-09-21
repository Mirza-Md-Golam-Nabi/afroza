@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

    <form action="{{ route('admin.brand.store') }}" method="post" class="mt-3">
        {{ csrf_field() }}
        <div class="form-group">
          <label for="brandName">Brand Name</label>
          <input type="text" name="brandName" class="form-control" id="brandName" autocomplete="off" autofocus>
        </div>
        <input type="submit" class="btn btn-primary" value="Submit">    
    </form>



@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection