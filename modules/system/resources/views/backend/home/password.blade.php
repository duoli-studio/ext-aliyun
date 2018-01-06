@extends('system::backend.tpl.default')
@section('backend-breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>修改密码</h2>
        </div>
    </div>
@endsection
@section('backend-main')
    <div class="ibox">
        <div class="ibox-content">
            {!! Form::open(['id'=> 'form_password', 'class' => 'form-horizontal']) !!}
                <div class="form-group">
                    {!! Form::label('old_password', '老密码', ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::password('old_password',['class' => 'form-control']) !!}</div>
                </div>

                <div class="form-group">
                    {!! Form::label('password', '密码', ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::password('password',['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('password_confirmation', '重复密码', ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::password('password_confirmation',['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-2 col-lg-offset-2">
                        {!! Form::button('修改密码', ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
    <script>
		requirejs(['jquery', 'poppy/util', 'jquery.validation', 'jquery.form'], function($, util) {
			var conf = util.validate_config({
				rules : {
					'old_password'          : {
						required : true
					},
					'password'              : {
						required : true
					},
					'password_confirmation' : {
						required : true,
						equalTo  : '#password'
					}
				}
			}, true, 'table');
			$('#form_password').validate(conf);
		});
    </script>
@endsection