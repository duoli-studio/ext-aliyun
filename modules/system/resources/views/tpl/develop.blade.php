@extends('system::tpl.default')
@section('title', $_title ?? '')
@section('head-css')
    {!! Html::style('resources/css/basic.css') !!}
    {!! Html::style('resources/css/develop.css') !!}
@endsection
@section('head-script')
    @include('system::tpl.inc_requirejs')
@endsection
@section('body-main')
    @include('system::tpl.inc_toastr')
    <div class="container">
        @yield('develop-main')
    </div>
@endsection
@section('footer-script')
    <script>requirejs(['jquery', 'bt3', 'poppy/cp'])</script>
@endsection