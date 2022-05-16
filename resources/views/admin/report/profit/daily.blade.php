@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

<div>
   Calender : <span id="year">{{ date("F", mktime(0, 0, 0, $month)) }} - {{ $year }}</span>
</div>

<table class="table table-striped mt-3">
  <thead>
    <tr>
      <th class="text-center" scope="col">Date</th>
      <th class="text-center" scope="col">Day</th>
      <th class="text-center" scope="col" style="text-align:center;">Profit</th>
    </tr>
  </thead>
  <tbody>
  	@php $i=0; @endphp
  	@foreach($profitData as $data)
    <tr>
      <td class="text-left">{{ $data->date }}</td>
      <td class="text-left">{{ $data->day }}</td>
      <td class="text-right" style="text-align:right;">{{ number_format($data->profit, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>



@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection