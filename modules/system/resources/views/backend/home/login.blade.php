@extends('system::tpl.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/backend.css') !!}
@endsection
@section('head-script')
    @include('ext-fe::requirejs')
@endsection
@section('body-class', 'gray-bg')
@section('body-main')
    @include('system::tpl.inc_toastr')
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name">&nbsp;</h1>
            </div>
            <h3>管理后台</h3>
            <p>后台管理系统</p>
            {!! Form::open(['class'=> 'm-t']) !!}
            <div class="form-group">
                {!! Form::text('username', null, ['class'=> 'form-control', 'placeholder'=> '用户名']) !!}
            </div>
            <div class="form-group">
                {!! Form::password('password', ['class'=> 'form-control', 'placeholder'=> '密码']) !!}
            </div>
            {!! Form::button('登录', ['class'=> 'btn btn-primary block full-width m-b J_submit']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection
@section('footer-script')
    <script>requirejs(['poppy/backend/cp']);</script>
@endsection