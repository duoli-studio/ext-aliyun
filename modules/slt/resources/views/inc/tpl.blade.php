@extends('lemon.template.default')
@section('head-css')
    {!! Html::style('project/sour/css/bt3.css') !!}
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