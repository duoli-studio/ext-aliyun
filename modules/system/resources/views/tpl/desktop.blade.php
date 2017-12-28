@extends('lemon.template.default')
@section('head-css')
	{!! Html::style('assets/css/3rd/font-awesome.css') !!}
	{!! Html::style('assets/css/3rd/animate.css') !!}
	{!! Html::style('assets/css/lemon/shopnc_skin.css') !!}
@endsection
@section('body-main')
	@include('lemon.inc.toastr')
	@yield('desktop-main')
@endsection
@section('script-cp')
	<script>require(['lemon/shopnc/cp']);</script>
    <script>
    require(['jquery', 'jquery.layer'], function ($, layer) {
        $('body').on('keydown',function(event){
            if(event.keyCode == 27){ // esc
                layer.closeAll();
            }
        });
    });
    </script>
@endsection
