@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

    <form action="{{ route('types.store') }}" method="post" class="mt-3">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="type_name">Type Name</label>
            <input type="text" name="type_name" class="form-control" id="type_name" autocomplete="off" autofocus required value="{{ old('type_name') }}">
            @if ($errors->has('type_name'))
                <span style="color:red;">
                    {{ $errors->first('type_name') }}
                </span>
            @endif
        </div>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>



@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection
