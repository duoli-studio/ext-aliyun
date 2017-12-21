@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        {{trans('desktop.edit_password')}}
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <form action="{{route('dsk_lemon_home.password')}}" data-rel="account" method="post" id="form_password"
                  class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="account_id" value="{{$_pam['account_id']}}">

                <div class="form-group">
                    {!! Form::label('account_name', trans('desktop.account_name'), ['class'=>'col-lg-2 control-label strong place']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('account_name', $_pam['account_name'], ['disabled', 'readonly','class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('old_password', '老密码', ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::password('old_password',['class' => 'form-control']) !!}</div>
                </div>

                <div class="form-group">
                    {!! Form::label('password', trans('desktop.password'), ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::password('password',['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('password_confirmation', trans('desktop.password_confirmation'), ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::password('password_confirmation',['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('', '', ['class'=>'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::button('修改密码', ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                    </div>
                </div>
            </form>
            <script>
				require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
					var conf = util.validate_conf({
						rules: {
							'old_password'         : {
								required: true
							},
							'password'             : {
								required: true
							},
							'password_confirmation': {
								required: true,
								equalTo : '#password'
							}
						}
					}, 'bt3');
					$('#form_password').validate(conf);
				});
            </script>
        </div>
    </div>
@endsection