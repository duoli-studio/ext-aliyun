@extends('lemon.template.default')
@section('head-css')
	<link rel="stylesheet" type="text/css" href="{!! assets('css/3rd/font-awesome.css') !!}" media="all" />
	<link rel="stylesheet" type="text/css" href="{!! assets('css/lemon/shopnc_skin.css') !!}" media="all" />
@endsection
@section('body-main')
	@include('lemon.inc.toastr')
	@yield('desktop-main')
@endsection
@section('script-cp')
	<script>require(['lemon/shopnc/cp']);</script>
@endsection
