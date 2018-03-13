@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('resources/css/basic.css') !!}
    {!! Html::style('resources/css/backend.css') !!}
@endsection
@section('head-script')
    @include('system::tpl.inc_requirejs')
@endsection
@section('body-main')
    @include('system::tpl.inc_toastr')
    <div id="wrapper">
        @include('system::backend.tpl.inc_nav')
        <div id="page-wrapper" class="gray-bg">
            @include('system::backend.tpl.inc_top')
            @yield('backend-breadcrumb')
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-content">
                        @yield('backend-main')
                    </div>
                </div>
            </div>
            @include('system::backend.tpl.inc_footer')
        </div>
    </div>
@endsection
@section('footer-script')
    <script>requirejs(['poppy/backend/cp']);</script>
@endsection