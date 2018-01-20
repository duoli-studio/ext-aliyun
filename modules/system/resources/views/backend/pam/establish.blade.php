@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::open(['id' => 'form_item', 'class' => 'form-horizontal']) !!}
    {!!Form::hidden('type', $type)!!}
    <div class="form-group">
        {!! Form::label('username', '用户名', ['class' => '  col-lg-2 control-label']) !!}
        <div class="col-lg-2">
            {!! Form::text('username', null, ['class' => 'form-control']) !!}
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
        {!! Form::label('role_name', '用户角色', ['class' => 'col-lg-2 control-label  ']) !!}
        <div class="col-lg-2">
            {!! Form::select('role_name', $roles, null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-2 col-lg-offset-2">
            {!! Form::button('添加', ['class'=>'btn btn-info J_submit', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection