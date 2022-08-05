@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

<form action="{{ route('products.update', $product) }}" method="post" class="mt-3">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="type_id">Type Name *</label>
        <select name="type_id" id="type_id" class="form-control" required>
            @foreach($types as $type)
                @if($product->type_id == $type->id)
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
        <label for="category_id">Category Name *</label>
        <select name="category_id" id="category_id" class="form-control" required>
            @foreach($categories as $category)
                @if($product->category_id == $category->id)
                    <option value="{{ $category->id }}" selected>{{ $category->category_name }}</option>
                @else
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endif
            @endforeach
        </select>
        @if ($errors->has('category_id'))
            <span style="color:red;">
                {{ $errors->first('category_id') }}
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="brand_id">Brand Name *</label>
        <select name="brand_id" id="brand_id" class="form-control" required>
            @foreach($brands as $brand)
                @if($product->brand_id == $brand->id)
                    <option value="{{ $brand->id }}" selected>{{ $brand->brand_name }}</option>
                @else
                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                @endif
            @endforeach
        </select>
        @if ($errors->has('brand_id'))
            <span style="color:red;">
                {{ $errors->first('brand_id') }}
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="product_name">Product Name *</label>
        <input type="text" name="product_name" id="product_name" value="{{ $product->product_name }}" class="form-control" required autocomplete="off">
        @if ($errors->has('product_name'))
            <span style="color:red;">
                {{ $errors->first('product_name') }}
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="main_unit">Main Unit *</label>
        <input type="text" name="main_unit" id="main_unit" value="{{ $product->main_unit }}" class="form-control" required>
        @if ($errors->has('main_unit'))
            <span style="color:red;">
                {{ $errors->first('main_unit') }}
            </span>
        @endif
    </div>
    <div class="form-group">
        <label for="">Others Unit</label>
        <div id="othersUnit">
            <?php
                $others_unit = json_decode($product->others_unit);
                $i = 0;
                $counter = count($others_unit);
            ?>
            @foreach($others_unit as $unit)
            @php $i++; @endphp
            @if($counter != $i)
            <div class="d-flex justify-content-around mb-1" id="div{{ $i }}">
                <input type="text" name="others_unit_value[]" value="{{ $unit->unit_value }}" class="form-control mr-1" placeholder="Unit Value">
                <input type="text" name="others_unit_name[]" value="{{ $unit->unit_name }}" class="form-control ml-1" placeholder="Unit Name">
                @if($i == 1)
                    <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>
                @else
                    <span class="btn btn-danger ml-1 btn_remove_product" id="{{ $i }}">X</span>
                @endif
            </div>
            @endif
            @endforeach
        </div>
        <span class="btn btn-success btn-sm mt-2" id="addmore">Add More Unit</span>
    </div>
    <div class="form-group">
        <label for="warning">Warning <small class="text-muted">(value should be a number)</small></label>
        <input type="text" name="warning" id="warning" value="{{ $product->warning }}" class="form-control">
        @if ($errors->has('warning'))
            <span style="color:red;">
                {{ $errors->first('warning') }}
            </span>
        @endif
    </div>
    <input type="submit" class="btn btn-primary" value="Update">
</form>


@endsection

@section('extrascript')

<script>


    $(document).ready(function(){
        var i = 100;
        $('#type_id').change(function(){
            var typeId = $('#type_id').val();
            if(typeId != ''){
                $.ajax({
                    url: "{{ route('general.category.fetch') }}?type-id=" + typeId,
                    method: 'GET',
                    success: function(data) {
                        $('#category_id').html(data);
                    }
                });
            }
        });

        $('#addmore').click(function(){
            i++;
            var data = `<div class="d-flex justify-content-around mb-1" id=div${i}>
                <input type="text" name="others_unit_value[]" class="form-control mr-1" placeholder="Unit Value" required>
                <input type="text" name="others_unit_name[]" class="form-control ml-1" placeholder="Unit Name" required>
                <span class="btn btn-danger ml-1 btn_remove_product" id="${i}">X</span>
            </div>`;
           $('#othersUnit').append(data);
        });
    });

    $(document).on('click', '.btn_remove_product', function(){
        var button_id = $(this).attr("id");
        $('#div'+button_id).remove();
    });

</script>

@endsection
