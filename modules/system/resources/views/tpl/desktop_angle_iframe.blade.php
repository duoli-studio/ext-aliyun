@extends('lemon.template.default')
@section('head-css')
    {!! Html::style('assets/css/3rd/font-awesome.css') !!}
    {!! Html::style('assets/css/3rd/simple-line-icons.css') !!}
    {!! Html::style('assets/css/3rd/angle/bt3.css', ['id' => 'bscss']) !!}
    {!! Html::style('assets/css/3rd/angle/angle.css', ['id' => 'maincss']) !!}
@endsection
@section('body-start')
    @if(\App\Lemon\Repositories\System\SysCookie::get('angle_jq-toggleState'))
    <body class="{!! \App\Lemon\Repositories\System\SysCookie::get('angle_jq-toggleState') !!}">
    @endif
@endsection
@section('body-main')
    @include('lemon.inc.toastr')
    <div class="pd5">
        <div class="panel panel-default">
            <div class="panel-body text-sm">
                @yield('desktop-main')
            </div>
        </div>
    </div>
@endsection
@section('script-cp')
    <script>
    require(['jquery', 'bt3','lemon/cp', 'lemon/3rd/angle'])
    </script>
@endsection
