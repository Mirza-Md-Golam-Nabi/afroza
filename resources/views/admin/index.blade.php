@extends('admin.layout.app')

@section('maincontent')

<!-- Main Content -->

<div class="d-flex justify-content-between flex-wrap">
   
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