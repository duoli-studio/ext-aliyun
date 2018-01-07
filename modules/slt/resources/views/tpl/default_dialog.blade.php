@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/slt.css') !!}
@endsection
@section('head-script')
    @include('ext-fe::requirejs')
@endsection
@section('body-class') slt--page_dialog @yield('body-dialog_class') @endsection
@section('body-main')
    <div class="container">
        @yield('tpl-main')
    </div>
    <script>
		require(['jquery', 'bt3', 'slt/cp'])
    </script>
@endsection