@extends('slt::tpl.default')
@section('tpl-main')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        {!! Form::open(['id' => 'form_login']) !!}
                        <h4 class="form-title">用户登录</h4>
                        {!! Form::hidden('_go', \Url::previous()) !!}
                        <div class="form-group">
                            <label>账号/手机号/邮箱</label>
                            {!! Form::text('passport', null, ['placeholder' => '请输入手机号/邮箱/用户名', 'class'=> 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label>密码</label>
                            {!!Form::password('password', ['placeholder' => '请输入密码', 'class'=> 'form-control'])!!}
                        </div>
                        <div class="form-group clearfix">
                            <label class="pull-left">
                                {!!Form::checkbox('remember_me', 1, 1, ['id'=> 'remember_me'])!!}
                                记住登录状态
                            </label>
                            <a class="pull-right" href="{!! route('slt:user.forgot_password') !!}">找回密码</a>
                        </div>
                        <div class="form-group">
                            {!! Form::button('登录', ['class'=> 'btn btn-info form-control J_submit', 'type'=> 'submit']) !!}
                        </div>
                        <div class="form-group text-center">
                            <a href="{!! route('slt:user.register') !!}">注册新用户</a>
                        </div>
                        {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('slt::tpl.inc_footer')
@endsection