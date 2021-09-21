@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

@include('msg')

<form action="{{ route('admin.product.store') }}" method="post" class="mt-3">
    {{ csrf_field() }}
    <div class="form-group">
        <label for="type_id">Type Name *</label>
        <select name="type_id" id="type_id" class="form-control" required>
            <option value="">Please Select One</option>
            @foreach($typeList as $list)
            <option value="{{ $list->id }}">{{ $list->type_name }}</option>
            @endforeach
        </select> 
    </div>
    <div class="form-group">
        <label for="category_id">Category Name *</label>
        <select name="category_id" id="category_id" class="form-control" required>
            <option value="">Please Select One</option>
        </select> 
    </div>   
    <div class="form-group">
        <label for="brand_id">Brand Name *</label>
        <select name="brand_id" id="brand_id" class="form-control" required>
            <option value="">Please Select One</option>
            @foreach($brandList as $list)
            <option value="{{ $list->id }}">{{ $list->brand_name }}</option>
            @endforeach
        </select> 
    </div>
    <div class="form-group">
        <label for="product_name">Product Name *</label> 
        <input type="text" name="product_name" id="product_name" class="form-control" required onkeyup="productCheck()" autocomplete="off">
        <div id="productCheck"></div>
    </div>
    <div class="form-group">
        <label for="main_unit">Main Unit *</label> 
        <input type="text" name="main_unit" id="main_unit" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="">Others Unit</label> 
        <div id="othersUnit">
            <div class="d-flex justify-content-around mb-1">            
                <input type="text" name="others_unit_value[]" class="form-control mr-1" placeholder="Unit Value">
                <input type="text" name="others_unit_name[]" class="form-control ml-1" placeholder="Unit Name">
                <span class="btn ml-1">&nbsp;&nbsp;&nbsp;</span>             
            </div>
        </div>
        <span class="btn btn-success btn-sm mt-2" id="addmore">Add More Unit</span>
    </div>
    <div class="form-group">
        <label for="warning">Warning <small class="text-muted">(value should be a number)</small></label>        
        <input type="text" name="warning" id="warning" class="form-control">
    </div>
    <input type="submit" class="btn btn-primary" value="Submit">    
</form>





@endsection

@section('extrascript')

<script>

    function productCheck(){
        var product_name = document.getElementById('product_name').value;

        if(product_name){
            $.ajax({ 
                url: "{{ route('general.product.check') }}?product=" + product_name,
                method: 'GET',
                success: function(data) {
                    $('#productCheck').html(data);
                }
            });
        }else{
            $('#productCheck').html("");
        }
    }

    $(document).ready(function(){
        var i = 1;
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
            var data = `<div class="d-flex justify-content-around mb-1" id="div${i}">            
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