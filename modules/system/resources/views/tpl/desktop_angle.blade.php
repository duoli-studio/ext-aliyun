@extends('lemon.template.default')
@section('head-css')
    {!! Html::style('assets/css/3rd/font-awesome.css') !!}
    {!! Html::style('assets/css/3rd/simple-line-icons.css') !!}
    {!! Html::style('assets/css/3rd/angle/bt3.css', ['id' => 'bscss']) !!}
    {!! Html::style('assets/css/3rd/angle/angle.css', ['id' => 'maincss']) !!}
    @if(\App\Lemon\Repositories\System\SysCookie::get('angle_theme'))
        <link id="autoloaded-stylesheet" rel="stylesheet" href="{!! \App\Lemon\Repositories\System\SysCookie::get('angle_theme') !!}">
    @endif
@endsection
@section('body-start')

    @if(\App\Lemon\Repositories\System\SysCookie::get('angle_jq-toggleState'))
        <body class="{!! \App\Lemon\Repositories\System\SysCookie::get('angle_jq-toggleState') !!}">
    @endif
@endsection
@section('body-main')
	@include('lemon.inc.toastr')
    <div class="wrapper">
        @include('lemon.inc.angle_top')
        @include('lemon.inc.angle_side')
        @include('lemon.inc.angle_offsidebar')
        <section>
            <!-- Page content-->
            <div class="content-wrapper text-sm">
                @yield('desktop-main')
            </div>
        </section>
    </div>

@endsection
@section('script-cp')
    <script>
    require(['jquery', 'jquery.layer'], function ($, layer) {
        $('body').on('keydown',function(event){
            if(event.keyCode == 27){ // esc
                layer.closeAll();
            }
        });
    });
    </script>
    <script>
    require(['jquery', 'modernizr', 'bt3', 'js-storage', 'parsley', 'lemon/3rd/angle', 'lemon/cp'])
    </script>
@endsection
