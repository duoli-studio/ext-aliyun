@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.plugin_ip.header')
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::open(['id' => 'form_ip','class' => 'form-horizontal']) !!}
            <div class="form-group">
                {!! Form::label('ip_addr', trans('desktop.ip.ip'), ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::text('ip_addr',null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('','', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                </div>
            </div>
            {!! Form::close() !!}
            <script>
			require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
				var conf = util.validate_conf({
					rules   : {
						'ip_addr': {
							required: true,
							ipv4    : true,
							remote  : "{{route('support_validate.allow_ip_available')}}"
						}
					},
					messages: {
						ip_addr: {
							remote: 'ip 地址有重复'
						}
					}
				}, 'bt3');
				$('#form_ip').validate(conf);
			});
            </script>
        </div>
    </div>
@endsection