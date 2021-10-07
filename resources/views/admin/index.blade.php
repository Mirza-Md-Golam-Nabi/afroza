@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

<style>
   .contain{
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-gap: 5px;
   }
</style>

<div>
   <h4 style="text-align: center;" class="p-2 bg-success text-white">Profit</h4>
   <div class="contain">
      <div style="text-align: center;">This Month<br>{{ number_format($profit['profit0'], 1) }}</div>
      <div style="text-align: center;">Prev Month<br>{{ number_format($profit['profit1'], 1) }}</div>
   </div>
</div>
<hr>
<div class="d-flex align-items-stretch flex-wrap">
   
   <?php 
      $divHead = "Daily Report";
      $url = "admin.report.date";
   ?>
   @include('admin.includes.div')
   <?php 
      $divHead = "Weekly Report";
      $url = "admin.report.weekly";
   ?>
   @include('admin.includes.div')
   <?php 
      $divHead = "Last 3 Month Report";
      $url = "admin.report.last.3.month";
   ?>
   @include('admin.includes.div')
   <?php 
      $divHead = "Monthly Report";
      $url = "admin.report.product.list";
   ?>
   @include('admin.includes.div')
   <?php 
      $divHead = "Yearly Report";
      $url = "admin.report.yearly";
   ?>
   @include('admin.includes.div')

   @foreach(SessionController::brandList() as $brand)
   <?php 
      $divHead = $brand->brand_name." Report";
      $url = 'admin.report.company';
      $param = ['name'=>$brand->brand_name];
   ?>
   @include('admin.includes.divWithParam')
   @endforeach



   
</div>


@endsection

@section('extrascript')

<!-- Extra Script -->

@endsection