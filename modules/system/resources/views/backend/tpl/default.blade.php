@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/3rd/font-awesome.css') !!}
    {!! Html::style('assets/css/lemon/backend/cp.css') !!}
@endsection
@section('body-main')
    @include('system::tpl.inc_toastr')
    <div id="wrapper">
        @include('system::backend.tpl.inc_nav')
        <div id="page-wrapper" class="gray-bg">
            @include('system::backend.tpl.inc_top')
            @yield('backend-breadcrumb')
            <div class="wrapper wrapper-content animated fadeInRight">
                @yield('backend-main')
            </div>
            @include('system::backend.tpl.inc_footer')
        </div>
    </div>
@endsection
@section('script-cp')
    <script>requirejs(['lemon/backend/cp']);</script>
@endsection