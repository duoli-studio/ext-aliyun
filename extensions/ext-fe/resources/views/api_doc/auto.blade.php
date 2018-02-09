@extends('system::tpl.develop')
@section('body-main')
    <div class="container">
        @include('ext-fe::api_doc.nav')
        @if (!$data['file_exists'])
            <div class="alert alert-danger">
                Api Doc 文件不存在, 请运行 <code>php artisan ext:fe_doc api</code> 来生成 Api 文档
            </div>
        @else
            <div class="row mt10">
                <div class="col-sm-6">
                    {{-- ajax 方式, 便于调试, 需要服务器配置跨域 --}}
                    {!! Form::model(isset($data['params']) ? $data['params'] : null,['url' => $api_url.$data['current']->url, 'method' => $data['current']->type,  'id'=> 'form_auto']) !!}
                    {{-- 使用 curl 方式
                    {!! Form::model(isset($data['params']) ? $data['params'] : null,['route' => 'dev_api.auto', 'id'=> 'form_auto']) !!}
                    {!! Form::hidden('u_url' , $api_url.$data['current']->url) !!}
                    {!! Form::hidden('u_method' , $data['current']->type) !!}
                    --}}
                    @if(isset($data['access_token']))
                        <p class="alert alert-info form-group">
                            {!! Form::label('access_token', '当前 Access Token:') !!}
                            {!! Form::text('access_token',$data['access_token'], ['class' => 'form-control J_calc', 'readonly'=> true]) !!}
                            {!! Form::label('sign', 'Sign:') !!}
                            {!! Form::text('sign','', ['class' => 'form-control', 'readonly'=> true]) !!}
                        </p>
                    @endif

                    @if (isset($data['current_params']) && $data['current_params'])
                        @foreach($data['current_params'] as $param)
                            @if ($param->field != 'access_token')
                                <div class="form-group">
                                    {!! ($param->optional ? '' : '<span style="color:red">*</span>') !!}
                                    {!! Form::label($param->field, strip_tags($param->description)) !!}
                                    (
                                    {!! strip_tags($param->type) !!}
                                    @if(isset($param->size)){ {!! $param->size !!} }@endif
                                    @if(isset($param->allowedValues))
                                        { {!! \Duoli\ExtBase\Helper\ArrHelper::combine($param->allowedValues) !!} }@endif
								    )
                                    [{!! $param->field !!}]
                                    {!! Form::text($param->field, null, ['class' => 'form-control J_calc']) !!}
                                </div>
                            @endif
                        @endforeach
                    @endif

                    @if (isset($fields))
                        @foreach($fields as $field)
                            <div class="form-group">
                                {!! ($field['is_require'] == 'N' ? '' : '<span style="color:red">*</span>') !!}
                                {!! Form::label($field['field_name'], strip_tags($field['field_title'])) !!}
                                (
                                {!! strip_tags($field['form_type']) !!}
                                [{!! $field['field_name'] !!}]
                                )
                                {!! Form::text($field['field_name'], null, ['class' => 'form-control ']) !!}
                            </div>
                        @endforeach
                    @endif

                    <div class="form-group">
                        {!! Form::button($data['current']->title, ['class' => 'btn btn-info', 'type'=>'submit', 'id'=>'submit']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="col-sm-6">
                    <div class="alert alert-success">
                        <div class="clearfix">
                            {!! Form::label('url', '访问地址{'.$data['current']->type.'}:') !!}
                            <span class="pull-right" id="return_desc">返回值</span>
                        </div>
                        {!! Form::text('url',$api_url.$data['current']->url, ['class' => 'form-control', 'readonly'=> true]) !!}
                        <div class="clearfix api-versions" id="api_version">
                            @foreach($data['versions'] as $kv => $version)
                                <a @if ($kv == $data['version']) class="current"
                                   @endif href="{!! route_url('', null, ['url' => $data['current']->url, 'method' => $data['current']->type, 'version' => $version]) !!}"> {{$version}} </a>
                            @endforeach
                        </div>
                    </div>
                    @if (count($success))
                        <div style="max-height: 200px;display: none;" id="return_param">
                            <table class="table table-narrow">
                                <tr>
                                    <td>字段</td>
                                    <td>类型</td>
                                    <td>描述</td>
                                </tr>
                                <tbody>
                                @foreach($success as $item)
                                    <tr>
                                        <td>{!! $item->field !!}</td>
                                        <td>{!! $item->type !!}</td>
                                        <td>{!! strip_tags($item->description) !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <pre id="J_result" style="display: none;margin-top:20px;"></pre>
                </div>
            </div>
            <script>
			requirejs(['jquery', 'json', 'poppy/util', 'jquery.layer', 'underscore', 'js-cookie', 'jquery.validation'], function($, JSON, util, layer, _, cookie) {
				function calc_sign() {
					var params = [];
					var str = '';

					function _sign(tip) {
						$('input[name=sign]').val(tip);
					}

					function _val(name) {
						return $('input[name=' + name + ']').val();
					}

					$('.J_calc').each(function(i, ele) {
						params.push($(ele).attr('name'));
					});

					params = _.without(params, 'sign');
					params.sort();

					_.each(params, function(key) {
						str += key + '=' + _val(key) + ','
					});
					console.log(str);
					str = str.slice(0, -1);
					var token = _val('access_token');
					_sign(token);
				}

				$(function() {
					calc_sign();
					$('.J_calc').bind('input propertychange', function() {
						calc_sign();
					});
					var conf = util.validate_config({
						submitHandler : function(form) {
							$('#J_result').text(
								'进行中...'
							).css('color', 'grey');
							$(form).ajaxSubmit({
								beforeSend : function(request) {
                                    @if(isset($data['access_token']))
									request.setRequestHeader("X-ACCESS-TOKEN", "{!! $data['access_token'] !!}");
                                    @endif
                                    @if(isset($data['version']))
									request.setRequestHeader("Accept", "application/{!! config('api.standardsTree').'.'.config('api.subtype').'.'.$data['version'].'+json' !!}");
                                    @endif
								},
								success    : function(data) {
									var objData = util.to_json(data);
									if (typeof objData.data != 'undefined') {
										var objSubData = objData.data;
										if (typeof objSubData.access_token != 'undefined') {
											cookie.set('dev_dailian#token', objSubData.access_token);
										}
									}

									$('#J_result').text(
										JSON.stringify(util.to_json(data), null, '  ')
									).show(300).css('color', 'green');
								},
								error      : function(data) {
									$('#J_result').text(
										JSON.stringify(JSON.parse(data), null, '  ')
									).show(300).css('color', 'red');
								}
							});
						},
						'rules'       : {
                    @if (isset($data['current_params']) && $data['current_params'])
                    @foreach($data['current_params'] as $param)
                    {!! $param->field !!}:
					{
						required: {!! ($param->optional ? 'false' : 'true') !!} }
				,
                    @endforeach
                            @endif
						_pre:{
						required:false
					}
				}
				},true)
					;
					$('#form_auto').validate(conf);
				});
				$('#return_desc').on('click', function() {
					layer.open({
						type    : 1,
						shade   : false,
						title   : '返回参数',
						area    : '400px',
						maxmin  : true,
						shift   : 5,
						offset  : 'rb',
						content : $('#return_param'), //捕获的元素
						cancel  : function(index) {
							layer.close(index);
						}
					});
				});

			})
            </script>
        @endif
    </div>
@endsection