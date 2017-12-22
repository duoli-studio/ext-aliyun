@extends('slt::inc.default')
@section('head-css')
    {!! Html::style('assets/css/libs/bt3/3.3.7/bootstrap.css') !!}
    {!! Html::style('assets/css/3rd/animate.css') !!}
    {!! Html::style('assets/css/font/font-awesome.css') !!}
    {!! Html::style('project/sour/css/app.css') !!}
@endsection
@section('body-main')
    @include('slt::inc.nav')
    <div style="margin-top:55px;"></div>
    @yield('tpl-main')
    <script>
		require(['jquery', 'bt3', 'sour/cp'])
    </script>
@endsection