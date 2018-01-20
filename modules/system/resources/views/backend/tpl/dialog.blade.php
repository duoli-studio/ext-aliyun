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
    <div class="gray-bg">
        <div class="panel panel-default">
            <div class="panel-body">
        @yield('backend-main')
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script>requirejs(['poppy/backend/cp']);</script>
@endsection