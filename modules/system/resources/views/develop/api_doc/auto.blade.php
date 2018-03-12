@extends('system::tpl.develop')
@section('body-main')
    <div class="container">
        @include('system::develop.api_doc.nav')
        @if (!$data['file_exists'])
            <div class="alert alert-danger">
                Api Doc 文件不存在, 请运行 <code>php artisan ext:fe_doc api</code> 来生成 Api 文档
            </div>
        @else
            <div class="row mt10" id="app">
                <div class="col-sm-6">
                    {{-- ajax 方式, 便于调试, 需要服务器配置跨域 --}}
                    {!! Form::model(isset($data['params']) ? $data['params'] : null,[
                        'url' => $api_url.'/'.$data['current']->url,
                        'method' => $data['current']->type,
                        'id'=> 'form_auto'
                    ]) !!}
                    {{-- 使用 curl 方式
                    {!! Form::model(isset($data['params']) ? $data['params'] : null,['route' => 'dev_api.auto', 'id'=> 'form_auto']) !!}
                    {!! Form::hidden('u_url' , $api_url.$data['current']->url) !!}
                    {!! Form::hidden('u_method' , $data['current']->type) !!}
                    --}}
                    @if(isset($data['token']))
                        <p class="alert alert-info form-group">
                            {!! Form::label('token', '当前 Token:') !!}
                            @if (Route::has('system:develop.cp.set_token'))
                                <a href="{!! route_url('system:develop.cp.set_token', null, ['type'=> $guard]) !!}"
                                   data-title="设置 Token {!! $guard !!}"
                                   class="J_iframe pull-right btn btn-info btn-sm">设置 Token</a>
                            @endif
                            @if (Route::has('system:develop.cp.api_login'))
                                <a href="{!! route_url('system:develop.cp.api_login', null, ['type'=> $guard]) !!}"
                                   data-title="登录 {!! $guard !!}"
                                   class="J_iframe pull-right btn btn-primary btn-sm mr10">登录</a>
                            @endif
                            {!! Form::text('token',$data['token'], [
                                'class' => 'form-control J_calc mt3',
                                'readonly'=> true,
                                'disabled'=> true,
                            ]) !!}
                        </p>
                    @endif

                    @if (isset($data['current_params']) && $data['current_params'])
                        @foreach($data['current_params'] as $param)
                            @if ($param->field != 'access_token')
                                @if (starts_with($param->field, ':'))
									<?php continue; ?>
                                @endif
                                <div class="form-group">
                                    <label for="field_{!! $param->field !!}">
                                        {!! ($param->optional ? '' : '<span style="color:red">*</span>') !!}
                                        {!! $param->field !!}
                                        ({!! strip_tags($param->type) !!})
                                    </label>
                                    &nbsp;&nbsp;
                                    <span>
                                        {!! strip_tags($param->description) !!}
                                        @if(isset($param->size))
                                            { {!! $param->size !!} }
                                        @endif
                                        @if(isset($param->allowedValues))
                                            { {!! \Poppy\Framework\Helper\ArrayHelper::combine($param->allowedValues) !!} }
                                        @endif
                                    </span>
                                    {!! Form::text($param->field, null, ['class' => 'form-control', 'id'=> 'field_'.$param->field]) !!}
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
                        <p class="alert alert-info">{
                            @{{ requestType }}
                            }
                            @{{ url }}
                            <i class="pull-right glyphicon glyphicon-list-alt" v-on:click="openParam"></i>
                        </p>
                        <div class="clearfix api-versions pt8">
                            @foreach($variables as $key => $item)
                                {!! Form::select($key, $item, null, [
                                    'class' => 'form-control w120 J_variable',
                                    'style' => 'display:inline-block',
                                    'v-on:change' => 'changeVariable',
                                    'placeholder' => '选择 '.$key,
                                ]) !!}
                            @endforeach
                        </div>
                        <div class="clearfix api-versions pt8" id="api_version">
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
                                    @if(isset($data['token']))
									request.setRequestHeader("Authorization", "Bearer {!! $data['token'] !!}");
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
                    @if (starts_with($param->field, ':'))
					<?php continue; ?>
                    @endif
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
				},
					true
				)
					;
					$('#form_auto').validate(conf);
				});
			})
            </script>
            <script>
			require(['vue', 'jquery', 'jquery.layer'], function(Vue, $, layer) {
				new Vue({
					el      : '#app',
					data    : {
						requestType : '{!! $data['current']->type !!}',
						url         : '{!! $api_url.'/'.$data['current']->url !!}',
						url_origin  : '{!! $api_url.'/'.$data['current']->url !!}',
						variables   : {}
					},
					methods : {
						changeVariable : function(e) {
							const self = this;
							var name = e.target.name;
							this.variables[name] = e.target.value;
							var url = this.url_origin;
							Object.keys(this.variables).forEach(function(name) {
								console.log(name);
								url = url.replace(':' + name, self.variables[name])
							});
							this.url = url;
							$('#form_auto').attr('action', this.url);
						},
						openParam      : function() {
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
						}
					}
				});
			})
            </script>
        @endif
    </div>
@endsection