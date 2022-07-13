@extends('admin.layout.app')
@section('maincontent')

@include('admin.includes.date.dateWithAddMore')

@include('admin.includes.date.attachMoreDate')

@include('admin.includes.date.addMoreButton')

@endsection

@section('extrascript')

@include('admin.includes.date.fetchMoreDate')

@endsection
