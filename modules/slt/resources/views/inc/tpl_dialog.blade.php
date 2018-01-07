@extends('lemon.template.default')
@section('head-css')
    {!! Html::style('project/sour/css/bt3.css') !!}
    {!! Html::style('assets/css/3rd/animate.css') !!}
    {!! Html::style('assets/css/font/font-awesome.css') !!}
    {!! Html::style('project/sour/css/app.css') !!}
    {!! Html::style(conf('backend::site.css_file')) !!}
@endsection
@section('body-class') sour--page_dialog @yield('body-dialog_class') @endsection
@section('body-main')
    <div class="container">
        @yield('tpl-main')
    </div>

    <script>
        require(['jquery', 'bt3', 'sour/cp'])
    </script>
@endsection