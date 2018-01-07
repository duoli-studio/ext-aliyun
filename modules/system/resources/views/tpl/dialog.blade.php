@extends('lemon.template.default')
@section('head-css')
{!! Html::style('assets/css/lemon/bt3.css') !!}
{!! Html::style('assets/css/lemon/font-awesome.css') !!}
{!! Html::style('assets/css/lemon/plugin.css') !!}
{!! Html::style('assets/css/lemon/animate.css') !!}
{!! Html::style('assets/css/lemon/inspinia.css') !!}
@endsection
@section('body-start')<body class="white-bg">@endsection
@section('body-main')
	@include('lemon.inc.toastr')
	<div id="wrapper">
		@yield('lemon_dialog-main')
	</div>
@endsection
@section('script-cp')

@endsection
