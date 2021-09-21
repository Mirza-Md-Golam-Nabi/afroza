@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

<form action="{{ route('admin.brand.update') }}" method="post" class="mt-3">
    {{ csrf_field() }}
    <input type="hidden" name="brand_id" value="{{ $brand->id }}">
    <div class="form-group">
        <label for="brandName">Brand Name</label>
        <input type="text" name="brandName" class="form-control" id="brandName" value="{{ $brand->brand_name }}" required autofocus autocomplete="off">
        @if ($errors->has('brand_name'))
            <span style="color:red;">
                {{ $errors->first('brand_name') }}
            </span>
        @endif
    </div>
    <input type="submit" class="btn btn-primary" value="Update">    
</form>

@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection