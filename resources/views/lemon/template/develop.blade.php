@extends('lemon.template.default')
@section('head-css')
	{!! Html::style('assets/css/3rd/bt3.css') !!}
	{!! Html::style('assets/css/3rd/font-awesome.css') !!}
	{!! Html::style('assets/css/lemon/develop.css') !!}
@endsection
@section('body-main')
	@include('lemon.inc.toastr')
	<div class="container">
		@yield('develop-main')
	</div>
@endsection
@section('script-cp')
	<script>requirejs(['jquery', 'bt3'])</script>
@endsection