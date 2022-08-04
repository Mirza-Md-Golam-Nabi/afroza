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

    <form action="{{ route('types.store') }}" method="post" class="mt-3">
        {{ csrf_field() }}
        <div class="form-group">
          <label for="typeName">Type Name</label>
          <input type="text" name="typeName" class="form-control" id="typeName" autocomplete="off" autofocus required value="{{ old('typeName') }}">
        </div>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>



@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection
