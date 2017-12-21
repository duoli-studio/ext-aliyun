@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_order.header')
    <ul class="nav nav-tabs" data-relation="order">
        <li role="presentation" class="active"><a href="#basic" aria-controls="basic" role="tab"
                                                  data-toggle="tab"><span>基本信息</span></a></li>
        <li role="presentation"><a href="#info" aria-controls="info" role="tab"
                                   data-toggle="tab"><span>接单信息(仅后台可见)</span></a></li>
        <li role="presentation"><a href="#price" aria-controls="price" role="tab" data-toggle="tab"><span>价格</span></a>
        </li>
        <li role="presentation"><a href="#fadan" aria-controls="fadan" role="tab" data-toggle="tab"><span>发单平台设定</span></a>
        </li>
    </ul>
    @if (isset($item))
        {!! Form::model($item,['route' => ['dsk_platform_order.edit', $item->order_id], 'id' => 'form_lol', 'class'=> 'form-horizontal']) !!}
    @else
        {!! Form::open(['route' => 'dsk_platform_order.create','id' => 'form_lol', 'class'=> 'form-horizontal']) !!}
    @endif
    <div class="tab-content" role="tablist">
        <div role="tabpanel" class="tab-pane active" id="basic">
            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                {!! Form::label('order_title', '订单标题:', ['class' => 'col-lg-2 control-label']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_title',null,['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('game_id', trans('游戏区服:'), ['class' => 'col-lg-2 control-label']) !!}
                                <div class="col-lg-2">
                                    {!! Form::select('game_id', \App\Models\GameName::kvLinear(), null, ['id'=>'game_id','class' => 'form-control']) !!}
                                </div>
                                <div class="col-lg-2" id="server_ctr">
                                    {!!Form::select('server_id', ['请选择游戏'], null, ['id' => 'server_id','class' => 'form-control'])!!}
                                </div>
                                <div class="col-lg-2" id="type_ctr">
                                    {!!Form::select('type_id', ['请选择代练类型'], null, ['id' => 'type_id','class' => 'form-control'])!!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_current', trans('当前游戏信息:'), ['class' => 'col-sm-2 control-label place strong']) !!}
                                <div class="col-sm-5">
                                    {!! Form::textarea('order_current', null, ['class'=> 'w360 small valid','rows'=>'3']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_content', trans('代练内容/要求:'), ['class' => 'col-sm-2 control-label validation strong']) !!}
                                <div class="col-sm-5">
                                    {!! Form::textarea('order_content', isset($default_order_content) ? $default_order_content : null, ['class'=> 'w360 small','rows'=>'4']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('game_account', trans('游戏账号:'), ['class' => 'col-sm-2 control-label strong validation']) !!}
                                <div class="col-sm-3">
                                    {!! Form::text('game_account',null,['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('game_pwd', trans('游戏密码:'), ['class' => 'col-sm-2 control-label strong validation']) !!}
                                <div class="col-sm-3">
                                    {!! Form::text('game_pwd',null,['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('game_actor', trans('角色名:'), ['class' => 'col-sm-2 control-label strong validation']) !!}
                                <div class="col-sm-3">
                                    {!! Form::text('game_actor',null,['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            快捷导入
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-lg-10">
                                        <div class="alert alert-info" role="alert"> 如果是淘宝定单，请将代练内容放在最后
                                            请发单人员在导入后注意检查一下！
                                            程序毕竟不是人，有可能产生错误。
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox c-checkbox col-lg-10">
                                        {!! Form::textarea('import', null, ['id' => 'J_importContent', 'style' => 'height: 270px;width:500px', 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-10">
                                        {!! Form::button('导入订单', ['class'=>'btn btn-info', 'id'=>'J_import']) !!}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="info">
            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                {!! Form::label('order_get_in_price', trans('接单价格(元):'), ['class' => 'col-lg-2 control-label strong validation']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_get_in_price', null, ['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('source_id', trans('订单来源:'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::select('source_id', \App\Models\GameSource::kvSourceTitle(), null, ['placeholder'=> '请选择订单来源','class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_get_in_number', trans('订单号:'), ['class' => 'col-lg-2 control-label strong validation']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_get_in_number',null,['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_get_in_mobile', trans('号主手机号:'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_get_in_mobile',null,['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_get_in_wangwang', trans('旺旺昵称:'), ['class' => 'col-lg-2 control-label strong validation']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_get_in_wangwang',null,['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_hours', trans('代练时限'), ['class' => 'col-lg-2 control-label strong validation']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_hours',null,['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('J_order_tags', trans('检索标签:'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    <div id="J_order_tags">@if (isset($item['order_tags']) && $item['order_tags']){!! order_tag_decode($item['order_tags']) !!}@endif</div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('enclosure',trans('上传附件'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::file('enclosure',['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_note', trans('备注:'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::textarea('order_note',null,['class'=> 'form-control','rows'=>'3']) !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="price">
            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                {!! Form::label('order_price', trans('发单价格(元):'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_price', null, ['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_add_price', trans('奖金(元):'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_add_price', null, ['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_safe_money', trans('安全保证金(元):'), ['class' => 'col-lg-2 control-label strong place']) !!} {!! Form::tip(trans('tip.safe_money')) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_safe_money', null, ['class'=> 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('order_speed_money', trans('效率保证金(元):'), ['class' => 'col-lg-2 control-label strong place']) !!}{!! Form::tip(trans('tip.speed_money')) !!}
                                <div class="col-lg-3">
                                    {!! Form::text('order_speed_money', null, ['class'=> 'form-control']) !!}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="fadan">
            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                {!! Form::label('tong_group', trans('发布频道:'), ['class' => 'col-lg-2 control-label strong place']) !!}
                                <div class="col-lg-3">
                                    {!! Form::radios('tong_group', \App\Models\PlatformOrder::kvTongGroup(),isset($item) ? $item->tong_group : \App\Models\PlatformOrder::TONG_GROUP_PUBLIC) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('', '', ['class' => 'col-lg-1 control-label strong validation']) !!}
            <div class="col-lg-3">
                {!! Form::button((!isset($item) ? '添加订单' : '修改订单'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                {!! Form::button('重置', ['class'=>'btn btn-info', 'type'=> 'reset']) !!}
            </div>
        </div>
    </div>
    <script>
		require(['jquery', 'fadan/util', 'lemon/util', 'jquery.validate', 'jquery.form', 'jquery.tagging-js'], function ($, helper, util) {
			$(function () {
				helper.server_html('game_id', 'server_ctr', 'server_id', {
					'id'       : 'server_id',
					'server_id': @if (isset($item) && isset($item['server_id']) && $item['server_id']) {!! $item['server_id'] !!} @else 0 @endif
				});
				helper.type_html('game_id', 'type_ctr', 'type_id', {
					'id'     : 'server_id',
					'type_id': @if (isset($item) && isset($item['type_id']) && $item['type_id']) {!! $item['type_id'] !!} @else 0 @endif
				});
			});
			$(function () {
				$('#J_order_tags').tagging({
					'tags-input-name': 'order_tags',
					'tag-box-class'  : 'sj-tagging'
				});
			});
			var conf = util.validate_conf({
				submitHandler: function (form) {
					$(form).ajaxSubmit({
						success: util.splash
					});
				},
				rules        : {
					'order_source' : {required: true},
					'order_title'  : {required: true},
					'order_number' : {
						number: true
					},
					'game_account' : {required: true},
					'game_pwd'     : {required: true},
					'game_area'    : {required: true},
					'game_actor'   : {required: true},
					'order_content': {required: true},
                    /* 'order_price' : {
                     required : true,
                     number : true
                     },
                     'order_safe_money' : {
                     required : true,
                     number : true
                     },
                     'order_speed_money' : {
                     required : true,
                     number : true
                     },*/
					'order_hours'  : {
						required: true,
						number  : true
					}
				}
			});
			$('#form_lol').validate(conf);
			$("#J_import").on('click', function () {
				var content = $('#J_importContent').val();
				if (content == '') {
					util.splash({
						status: 'error',
						msg   : '请输入导入的内容'
					});
				}
				$.post("{!! route('dsk_platform_order.import') !!}", {
					content: content,
					_token : $('meta[name="csrf-token"]').attr('content')
				}, function (resp) {
					var objResp = util.to_json(resp);
					for (var key in objResp) {
						if (key == 'order_content') {
							continue;
						}
						if ((objResp[key] && objResp[key] != 0 )) {
							var val = $('[name=' + key + ']').val();
							if (val && val.indexOf(objResp[key]) < 0) {
								// 已经有, 不做操作
								$('[name=' + key + ']').val(val + "\n" + objResp[key])
							} else {
								$('[name=' + key + ']').val(objResp[key])
							}

						}
					}
				});
				return false;
			})
		});
    </script>

@endsection