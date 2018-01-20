@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/develop.css') !!}
@endsection
@section('head-script')
    @include('ext-fe::requirejs')
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