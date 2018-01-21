@extends('system::tpl.dialog')
@section('develop-main')
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open([ 'id'=> 'form_auto','data-ajax'=>"true"]) !!}
            {!! Form::hidden('type', $type) !!}
            <div class="form-group">
                <label for="passport">账号</label>
                ( String ) [passport]
                <input class="form-control" name="passport"
                       data-rule-required="true"
                       type="text" id="passport">
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                ( String ) [password]
                <input class="form-control" name="password"
                       data-rule-required="true"
                       type="text" id="password">
            </div>
            <div class="form-group">
                <button class="btn btn-info J_validate" type="submit" id="submit">登录</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection