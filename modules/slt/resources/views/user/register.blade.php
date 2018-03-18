@extends('slt::tpl.default')
@section('tpl-main')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 bg-white">
                {!! Form::open(['route' => ['slt:user.register', $type]]) !!}
                <h4 class="form-title">注册账号</h4>
                <div class="form-group">
                    {!!Form::label($type, \System\Models\PamAccount::kvRegType($type))!!}
                    {!! Form::text($type, null, [
                        'placeholder' => '请输入'.\System\Models\PamAccount::kvRegType($type),
                        'class'=> 'form-control']) !!}
                </div>
                <div class="form-group">
                    <label>密码</label>
                    {!!Form::password('password', ['placeholder' => '请设置密码', 'id'=>  'password','class'=> 'form-control'])!!}
                    {!!Form::password('password_confirmation', ['placeholder' => '请确认密码','class'=> 'form-control'])!!}
                </div>
                <div class="form-group clearfix">
                    <label class="pull-left">
                        {!!Form::checkbox('agree', 1, null, ['id'=> 'agree'])!!}
                        同意并接受
                        <a class="font-green" href="#">#《服务条款》</a>
                    </label>
                    <span class="pull-right">
								{!!Form::button('注册', ['class' => 'btn btn-success J_submit', 'type' =>'submit'])!!}
							</span>
                </div>
                <div class="form-group text-center">
                    <a href="{!! route('slt:user.login') !!}">登录</a>
                </div>
                {!!Form::close()!!}
            </div>
        </div>
    </div>
    @include('slt::tpl.inc_footer')
@endsection