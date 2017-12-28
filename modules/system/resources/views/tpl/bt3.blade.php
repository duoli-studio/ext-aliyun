@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('assets/css/3rd/bt3.css') !!}
    {!! Html::style('assets/css/3rd/font-awesome.css') !!}
@endsection
@section('body-main')
    <div class="container">
        @yield('bootstrap-main')
    </div>
@endsection