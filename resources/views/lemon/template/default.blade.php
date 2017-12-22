<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>@if (isset($_title) && $_title) {!! $_title !!} - @endif</title>
	@section('head-meta') @show
	@section('head-css') @show
	@include('lemon.inc.requirejs')
</head>
@section('body-start')<body>@show

	@yield('body-main')

@section('script-cp') @show
</body>
</html>