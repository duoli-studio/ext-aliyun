@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('resources/css/basic.css') !!}
    {!! Html::style('resources/css/slt.css') !!}
@endsection
@section('head-script')
    @include('slt::tpl.inc_requirejs')
@endsection
@section('body-main')
    @include('slt::inc.nav')
    <div style="margin-top:70px;"></div>
    @yield('tpl-main')
    <script>
	require(['jquery', 'bt3', 'slt/cp'])
    </script>
@endsection