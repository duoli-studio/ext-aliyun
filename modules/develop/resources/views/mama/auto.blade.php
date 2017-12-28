@extends('lemon.template.develop')
@section('develop-main')
    @include('develop.mama.nav')
    @if (!$data['file_exists'])
        <div class="alert alert-danger">
            Api Doc 文件不存在, 请运行 <code>php artisan lemon:doc apidoc</code> 来生成 Api 文档
        </div>
    @else
        {{--{!! dd($data['content']) !!}--}}
        <div class="row mt10">
            @if ($user)
                <div class="col-sm-3">ID: {!! $user->account_id !!}</div>
                <div class="col-sm-3">用户名: {!! $user->account_name !!}</div>
                <div class="col-sm-3">金钱: {!! $front->money !!}</div>
                <div class="col-sm-3">冻结资金: {!! $front->lock !!}</div>
            @endif
        </div>
        <div class="row mt10">
            <div class="col-sm-6">
                {!! Form::model(isset($data['params']) ? $data['params'] : null,['url' => $api_url.$data['current']->url, 'method' => $data['current']->type,  'id'=> 'form_auto']) !!}
                @if(isset($account_id))
                    <div class="alert alert-info form-group form-inline form-group-sm">
                        {!! Form::text('ticket',$token, ['class' => 'form-control', 'readonly'=> true, 'style'=> 'width:400px;']) !!}
                        (ticket)
                        {{--
                        <br>
                        {!! Form::text('sign',$sign, ['class' => 'form-control', 'readonly'=> true, 'style'=> 'width:400px;', 'id'=>'sign']) !!}
                        (sign)
                        <br>
                        --}}
                    </div>
                @else
                    <p class="alert alert-warning">当前没有 发单用户 , 访问 <a
                                href="{!! route('dev_mama.login') !!}">这里</a>设置</p>
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
                                    { {!! \App\Lemon\Repositories\Sour\LmArr::toStr($param->allowedValues) !!} }@endif
								)
                                [{!! $param->field !!}]
                                {!! Form::text($param->field, null, ['class' => 'form-control J_sign_calc']) !!}
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
                            {!! Form::text($field['field_name'], null, ['class' => 'form-control J_sign_calc']) !!}
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
                <pre id="result" style="display: none;margin-top:20px;"></pre>
            </div>
        </div>
        <script>
            requirejs(['jquery', 'json', 'lemon/util', 'jquery.layer','js-cookie', 'jquery.validate'], function ($, JSON, util, layer, cookie) {
                $(function () {
                	/** sign 不必重新计算, 想多了
                    $('.J_sign_calc').on('change blur input propertychange', function () {
	                    var $form = $(this).parents('form');
                        var request_url = "{!! route('dev_mama.sign') !!}";
	                    var old_url = $form.attr('action');
	                    var old_method = $form.attr('method');
	                    if (!request_url) {
		                    request_url = old_url;
	                    }
	                    $form.attr('action', request_url);
	                    $form.attr('method', 'post');
	                    $form.ajaxSubmit({
		                    success: function (data) {
			                    $('#sign').val(data);
		                    }
	                    });
	                    $form.attr('action', old_url);
	                    $form.attr('method', old_method);
                    });
                    **/
                    var conf = util.validate_conf({
                        submitHandler: function (form) {
                            $('#result').text(
                                    '进行中...'
                            ).css('color', 'grey');
                            $(form).ajaxSubmit({
                                beforeSend: function (request) {
                                    @if(isset($data['token']))
                                     request.setRequestHeader("X-ACCESS-TOKEN", "{!! $data['token'] !!}");
                                    @endif
                                },
                                success   : function (data) {
                                	var objData = util.to_json(data);
                                	if (typeof objData.data != 'undefined') {
                                		var objSubData = objData.data;
                                		if (typeof objSubData.ticket != 'undefined') {
                                			cookie.set('dev_mama#token', objSubData.ticket);
                                        }
                                    }

                                	$('#result').text(
                                            JSON.stringify(util.to_json(data), null, '  ')
                                    ).show(300).css('color', 'green');
                                },
                                error     : function (jqXHR) {
                                	var data = jqXHR.responseText;
                                    $('#result').text(
                                            JSON.stringify(JSON.parse(data), null, '  ')
                                    ).show(300).css('color', 'red');
                                }
                            });
                        },
                        'rules'      : {
                            @if (isset($data['current_params']) && $data['current_params'])
                            @foreach($data['current_params'] as $param)
                            {!! $param->field !!}:{ required: {!! ($param->optional ? 'false' : 'true') !!} },
                            @endforeach
                            @endif
                            _pre:{required:false}
                        }
                    },'bt3_ajax');
                    $('#form_auto').validate(conf);
                });
                $('#return_desc').on('click', function () {
                    layer.open({
                        type   : 1,
                        shade  : false,
                        title  : '返回参数',
                        area   : '400px',
                        maxmin : true,
                        shift  : 5,
                        offset : 'rb',
                        content: $('#return_param'), //捕获的元素
                        cancel : function (index) {
                            layer.close(index);
                        }
                    });
                });
            })
        </script>
    @endif
@endsection