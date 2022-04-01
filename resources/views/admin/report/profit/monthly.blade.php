@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

<div>
   Year : <span id="year">{{ $year }}</span>
</div>

<table class="table table-striped mt-3">
  <thead>
    <tr>
      <th scope="col" style="text-align:center;">SN</th>
      <th scope="col">Month Name</th>
      <th scope="col" style="text-align:center;">Profit</th>
    </tr>
  </thead>
  <tbody>
  	@php $i=0; @endphp
  	@foreach($profitData as $data)
    <tr>
      <th scope="row" style="text-align:center;">{{ ++$i }}</th>
      <td>{{ $data->month }}</td>
      <td style="text-align:right;">{{ number_format($data->profit, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="mt-3">
	<form action="{{ route('admin.report.monthly.profit') }}" method="get">
		@csrf
		<select name="year" class="form-control px-4" style="width: auto; display:inline">
			<option value="">Select year</option>
	     @for($yearValue = 2021; $yearValue <= date("Y"); $yearValue++)
	     @if($yearValue == $year)
	     <option value="{{ $yearValue }}" selected>{{ $yearValue }}</option>
	     @else
	     <option value="{{ $yearValue }}">{{ $yearValue }}</option>
	     @endif
	     @endfor
	  </select>
	  <input type="submit" value="Submit" class="btn btn-sm btn-primary">
	</form>
</div>



@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection