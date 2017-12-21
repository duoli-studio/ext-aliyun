@extends('lemon.template.desktop')
@section('head-css')
    {!! Html::style('assets/css/3rd/font-awesome.css') !!}
    {!! Html::style('assets/css/3rd/simple-line-icons.css') !!}
    {!! Html::style('assets/css/3rd/angle/bt3.css', ['id' => 'bscss']) !!}
    {!! Html::style('assets/css/3rd/angle/angle.css', ['id' => 'maincss']) !!}
@endsection
@section('desktop-main')
    <div class="wrapper">
        <div class="block-center mt-xl wd-xl">
            <!-- START panel-->
            <div class="panel panel-dark panel-flat">
                <div class="panel-heading text-center">{{----}}
                    <a href="#">
                        {{--
                        {!! site('site_name') !!}
                        --}}
                    </a>
                </div>
                <div class="panel-body">
                    <p class="text-center pv">登录进行后续操作.</p>
                    {!! Form::open(['id' => 'form_login', 'class'=> 'mb-lg', 'role'=> 'form']) !!}
                    <div class="form-group has-feedback">
                        {!! Form::text('adm_name', null, ['class'=>'form-control', 'placeholder'=>'输入账号', 'required'=> true]) !!}
                        <span class="fa fa-envelope form-control-feedback text-muted"></span>
                    </div>
                    <div class="form-group has-feedback">
                        {!! Form::password('adm_pwd', ['class'=>'form-control', 'placeholder'=>'输入密码', 'required'=> true]) !!}
                        <span class="fa fa-lock form-control-feedback text-muted"></span>
                    </div>
                    {!! Form::button('登 录', ['class'=>'btn btn-block btn-primary mt-lg', 'type'=>'submit']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- END panel-->
            <div class="p-lg text-center">
                <span>&copy;</span>
                <span>2014 - 2017</span>
                <span>-</span>
                <span>山东猎象</span>
                <br>
                <span>
                    {{--
                    {!! site('copyright') !!}
                    --}}
                </span>
            </div>
        </div>
    </div>
@endsection
@section('cp')
    <script>require(['jquery', 'modernizr', 'bt3', 'js-storage', 'parsley', 'lemon/3rd/angle'])</script>
@overwrite