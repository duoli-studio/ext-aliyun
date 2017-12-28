@extends('lemon.template.develop')
@section('develop-main')
    @include('develop.up.nav')
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
            @else

            @endif
        </div>
        <div class="row mt10">
            <div class="col-sm-6">
                {{-- ajax 方式, 便于调试, 需要服务器配置跨域 --}}
                {!! Form::model(isset($data['params']) ? $data['params'] : null,['url' => $api_url.$data['current']->url, 'method' => $data['current']->type,  'id'=> 'form_auto']) !!}
                {{-- 使用 curl 方式
                {!! Form::model(isset($data['params']) ? $data['params'] : null,['route' => 'dev_api.auto', 'id'=> 'form_auto']) !!}
                {!! Form::hidden('u_url' , $api_url.$data['current']->url) !!}
                {!! Form::hidden('u_method' , $data['current']->type) !!}
                --}}
                @if(isset($data['up_user']))
                    <div class="alert alert-info form-group form-inline form-group-sm">
                        {!! Form::text('sign',$sign, ['class' => 'form-control', 'readonly'=> true, 'style'=> 'width:400px;']) !!}
                        (SIGN)<br>
                        {!! Form::text('app_key',$app_key, ['class' => 'form-control', 'readonly'=> true, 'style'=> 'width:400px;']) !!}
                        (APP KEY)<br>
                        {!! Form::text('timestamp',$timestamp, ['class' => 'form-control', 'readonly'=> true, 'style'=> 'width:400px;']) !!}
                        (TIMESTAMP)<br>
                        {!! Form::text('rand_str',$rand_str, ['class' => 'form-control', 'readonly'=> true, 'style'=> 'width:400px;']) !!}
                        (RAND STRING)<br>
                    </div>
                @else
                    <p class="alert alert-warning">当前没有 发单用户 , 访问 <a
                                href="{!! route('dev_up.up_user') !!}">这里</a>设置</p>
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
                                {!! Form::text($param->field, null, ['class' => 'form-control ']) !!}
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
                <pre id="result" style="display: none;margin-top:20px;"></pre>
            </div>
        </div>
        <script>
            requirejs(['jquery', 'json', 'lemon/util', 'jquery.layer', 'jquery.validate'], function ($, JSON, util, layer) {
                $(function () {

                    var conf = util.validate_conf({
                        submitHandler: function (form) {
                            $('#result').text(
                                    '进行中...'
                            ).css('color', 'grey');
                            $(form).ajaxSubmit({
                                beforeSend: function (request) {
                                    @if(isset($data['access_token']))
                                     request.setRequestHeader("X-ACCESS-TOKEN", "{!! $data['access_token'] !!}");
                                    @endif
                                    @if(isset($data['version']))
                                     request.setRequestHeader("Accept", "application/{!! config('api.standardsTree').'.'.config('api.subtype').'.'.$data['version'].'+json' !!}");
                                    @endif
                                },
                                success   : function (data) {
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
                    'bt3_ajax'
                    )
                    ;
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