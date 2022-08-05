@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')


<form action="{{ route('categories.update', $category->id) }}" method="post" class="mt-3">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="type_id">Type Name</label>
        <select name="type_id" id="type_id" class="form-control" required>
            <option value="">Please Select One</option>
            @foreach($types as $type)
                @if($category->type_id == $type->id)
                    <option value="{{ $type->id }}" selected>{{ $type->type_name }}</option>
                @else
                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                @endif
            @endforeach
        </select>
        @if ($errors->has('type_id'))
            <span style="color:red;">
                {{ $errors->first('type_id') }}
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="category_name">Category Name</label>
        <input type="text" name="category_name" class="form-control" id="category_name" value="{{ $category->category_name }}" required autocomplete="off">
        @if ($errors->has('category_name'))
            <span style="color:red;">
                {{ $errors->first('category_name') }}
            </span>
        @endif
    </div>
    <input type="submit" class="btn btn-primary" value="Update">
</form>


@endsection

@section('extrascript')

<script>

</script>

@endsection
