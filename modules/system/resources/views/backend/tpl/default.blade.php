@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/backend.css') !!}
@endsection
@section('head-script')
    @include('ext-fe::requirejs')
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
@section('footer-script')
    <script>requirejs(['poppy/backend/cp']);</script>
@endsection