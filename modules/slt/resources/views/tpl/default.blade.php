@extends('system::tpl.default')
@section('title', $_title ?? '')
@section('head-css')
    {!! Html::style('resources/css/basic.css') !!}
    {!! Html::style('resources/css/backend.css') !!}
    {!! Html::style('resources/css/slt.css') !!}
@endsection
@section('head-script')
    @include('slt::tpl.inc_requirejs')
@endsection
@section('body-class', 'top-navigation')
@section('body-main')
    @include('system::tpl.inc_toastr')
    <div id="wrapper">
        <div id="page-wrapper">
            @include('slt::tpl.inc_nav')
            @yield('tpl-main')
        </div>
    </div>
    @include('slt::tpl.inc_footer')
@endsection
@section('footer-script')
    <script>
	require(['jquery', 'bt3', 'slt/cp', 'poppy/backend/cp'])
    </script>
@endsection