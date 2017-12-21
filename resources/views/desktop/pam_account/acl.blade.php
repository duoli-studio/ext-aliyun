@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::model($auth, ['class'=> 'form-horizontal']) !!}
    {!! Form::hidden('account_id', $id) !!}
    <div class="form-group">
        <label class="col-sm-2 control-label">导出密码</label>
        <div class="col-sm-10">
            {!! Form::radios('auth[export_password]', ['否', '是'], null, ['inline'=> true]) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            {!! Form::button('保存', ['type'=> 'submit', 'class'=> 'J_submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection