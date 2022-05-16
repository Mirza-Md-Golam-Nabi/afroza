@extends('admin.layout.app')
@section('maincontent')

<div class="d-flex justify-content-between flex-wrap" id="addmore">
   @php $last_date = last($data)['original']; @endphp
   @foreach($data as $dat)
   <p style="border: 1px solid gray; padding:5px 10px;">
      <a href="{{ route($url, $dat['original']) }}">{{ $dat['date'] }}</a>
   </p>
   @endforeach
</div>
<span id="lastDate" data-lastdate="{{ $last_date }}"></span>
<div>
   <button class="btn btn-sm btn-success" onclick="addMore()">More Data</button>
</div>

@endsection

@section('extrascript')

<script>
   function addMore(){
      let select = document.getElementById("lastDate");
      let date = select.getAttribute('data-lastdate');
      if(date){
         $.ajax({
            url: "{{ route('general.more.date') }}?date=" + date,
            method: 'GET',
            success: function(data) {
               $("#addmore").append(data.data);
               document.getElementById("lastDate").setAttribute('data-lastdate', data.end_date);
               console.log(data.end_date);
               // $('#productCheck').html(data);
            }
         });
      }
   }
</script>

@endsection
