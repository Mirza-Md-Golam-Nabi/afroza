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

<form action="{{ route('types.update', $type->id) }}" method="post" class="mt-3">
    {{ csrf_field() }}
    @method('PUT')
    {{-- <input type="hidden" name="type_id" value="{{  }}"> --}}
    <div class="form-group">
        <label for="typeName">Type Name</label>
        <input type="text" name="typeName" class="form-control" id="typeName" value="{{ $type->type_name }}" required autofocus autocomplete="off">
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
