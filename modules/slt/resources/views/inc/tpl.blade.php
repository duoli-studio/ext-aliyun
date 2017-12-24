@extends('slt::inc.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/slt.css') !!}
@endsection
@section('body-main')
    @include('slt::inc.nav')
    <div style="margin-top:55px;"></div>
    @yield('tpl-main')
    <script>
		require(['jquery', 'bt3', 'slt/cp'])
    </script>
@endsection