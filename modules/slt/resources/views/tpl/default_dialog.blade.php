@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('resources/css/basic.css') !!}
    {!! Html::style('resources/css/slt.css') !!}
@endsection
@section('head-script')
    @include('slt::tpl.inc_requirejs')
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