@extends('lemon.template.bt3')
@section('bootstrap-main')
	@include('develop.api.nav')
	<div class="row mt10">
		<div class="col-sm-6">
			{!! Form::model($data,['url' => route('dev_api.access_token'), 'id'=> 'form_login']) !!}
			@if(isset($data['access_token']))
				<p class="alert alert-info">当前 Access Token : {!! $data['access_token'] !!}</p>
			@endif
			<div class="form-group">
				{!! Form::label('access_token', '新 Access Token') !!}
				{!! Form::text('access_token','', ['class' => 'form-control']) !!}
			</div>
			<div class="form-group">
				{!! Form::button('设置', ['class' => 'btn btn-info', 'type'=>'submit']) !!}
			</div>
			{!! Form::close() !!}
		</div>
		<div class="col-sm-6">
			<pre id="result" style="display: none;"></pre>
		</div>
	</div>
	<script>
	require(['jquery', 'lemon/util', 'json', 'jquery.validation'], function($, util, JSON){
		$(function(){
			var conf = util.validate_conf({
				submitHandler : function (form) {
					$(form).ajaxSubmit({
						success : function(data) {
							$('#result').text(
									JSON.stringify(data, null, '  ')
							).show(300);
						}
					});
				},
				rules:{
					access_token : {required : true}
				}
			}, 'bt3_ajax');
			$('#form_login').validate(conf);
		})
	})
	</script>
@endsection