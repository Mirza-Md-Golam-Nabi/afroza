@extends('admin.layout.app')
@section('maincontent')

<div class="d-flex justify-content-between flex-wrap">
   @foreach($data as $dat)
   <p style="border: 1px solid gray; padding:5px 10px;">
      <a href="{{ route($url, $dat['date']) }}">{{ $dat['date'] }}</a>
   </p>
   @endforeach
</div>

@endsection

@section('extrascript')


@endsection