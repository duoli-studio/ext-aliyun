@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::open(['id' => 'form_item', 'class' => 'form-horizontal']) !!}
    <div class="form-group">
        {!! Form::label('username', '用户名', ['class' => '  col-lg-2 control-label']) !!}
        <div class="col-lg-2">
            {!! Form::text('username', $pam->username, ['class' => 'form-control', 'readonly' => 'readonly', 'disabled'=> 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('password', '密码', ['class' => 'col-lg-2 control-label  ']) !!}
        <div class="col-lg-2">
            {!! Form::password('password', ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('password_confirmation', '重复密码', ['class' => 'col-lg-2 control-label  '.(!isset($item) ? '' : '')]) !!}
        <div class="col-lg-2">
            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-2 col-lg-offset-2">
            {!! Form::button('设置密码', ['class'=>'btn btn-info J_submit', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection