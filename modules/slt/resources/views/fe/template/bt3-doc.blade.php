@extends('lemon.template.default')
@section('head-css')
	<link rel="stylesheet" type="text/css" href="{!! assets('css/3rd/bt3.css') !!}" media="all" />
	<link rel="stylesheet" type="text/css" href="{!! assets('css/3rd/font-awesome.css') !!}" media="all" />
	<link rel="stylesheet" type="text/css" href="{!! assets('css/3rd/bt3-doc.css') !!}" media="all" />
@endsection
@section('body-main')
	@yield('bt3-doc-main')
@endsection
@section('script-cp')
	<script>requirejs(['jquery', 'bt3'])</script>
@endsection