@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

<form action="{{ route('brands.update', $brand) }}" method="post" class="mt-3">
    {{ csrf_field() }}
    @method('PUT')

    <div class="form-group">
        <label for="brand_name">Brand Name</label>
        <input type="text" name="brand_name" class="form-control" id="brand_name" value="{{ $brand->brand_name }}" required autofocus autocomplete="off">
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
