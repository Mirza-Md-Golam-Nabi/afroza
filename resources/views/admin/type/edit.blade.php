@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

<form action="{{ route('types.update', $type) }}" method="post" class="mt-3">
    {{ csrf_field() }}
    @method('PUT')

    <div class="form-group">
        <label for="type_name">Type Name</label>
        <input type="text" name="type_name" class="form-control" id="type_name" value="{{ $type->type_name }}" required autofocus autocomplete="off">
        @if ($errors->has('type_name'))
            <span style="color:red;">
                {{ $errors->first('type_name') }}
            </span>
        @endif
    </div>
    <input type="submit" class="btn btn-primary" value="Update">
</form>

@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection
