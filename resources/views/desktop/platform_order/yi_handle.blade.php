@can('yi_pub_cancel_apply', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_cancel_apply" data-title="撤销">
		<span> 申请撤销 </span>
	</button>
@endcan
@can('yi_pub_cancel_cancel', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_cancel_cancel" data-title="取消撤销">
		<span> 取消撤销</span>
	</button>
@endcan
@can('yi_sd_cancel_handle', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_sd_cancel_handle" data-title="同意撤销">
		<span> 撤销处理</span>
	</button>
@endcan
@can('yi_kf', $status)
	<a class=" btn btn-primary btn-sm mr3 J_request text-danger" data-confirm="确认申请客服介入?" href="{!! route('dsk_platform_status_yi.kf', [$status->id]) !!}">
		<span> 申请客服介入</span>
	</a>
@endcan
@can('yi_lock', $status)
	@if (!$status->yi_is_lock)
		<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_lock" data-title="锁定订单">
			<span><i class="fa fa-lock"> 锁定</i></span>
		</button>
	@else
		<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_unlock" data-title="解锁订单">
			<span><i class="fa fa-unlock"> 解锁 </i></span>
		</button>
	@endif
@endcan
@can('yi_ing', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_money" data-title="补款"><span>补款</span></button>
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_time" data-title="补时"><span>补时</span></button>
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_game_pwd" data-title="修改密码"><span>修改游戏密码</span></button>
@endcan

@can('yi_star', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_star" data-title="评价"><span>评价</span></button>
@endcan
@can('yi_progress', $status)
	<a class=" btn btn-primary btn-sm mr3 J_iframe text-info" data-shade_close="true" href="{!! route_url('dsk_platform_status_yi.progress_item', $status->id) !!}" data-title="更新进度"><span>更新进度</span></a>
@endcan
@can('yi_over', $status)
	<a class=" btn btn-primary btn-sm mr3 J_request text-danger " data-confirm="确认完成订单?" href="{!! route('dsk_platform_status_yi.over', [$status->id, $order->order_id]) !!}">
		<span> 确认完单</span>
	</a>
@endcan


<div id="detail_lock" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.lock', 'id' => 'form_lock']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td>变更状态</td>
			<td>锁定</td>
		</tr>
		<tr>
			<td class="w108">理由:</td>
			<td>
				{!! Form::textarea('note', null, ['class' => 'small']) !!}
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay">
					<span>提交</span>
				</button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
		var conf = util.validate_conf({
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					success : util.splash
				});
			}
		}, 'form');
		$('#form_lock').validate(conf);
	});
	</script>
</div>
<div id="detail_unlock" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.un_lock', 'id' => 'form_unlock']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td>变更状态</td>
			<td>解锁</td>
		</tr>
		<tr>
			<td class="w108">理由:</td>
			<td>
				{!! Form::textarea('note', null, ['class' => 'small']) !!}
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay">
					<span>提交</span>
				</button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
		var conf = util.validate_conf({
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					success : util.splash
				});
			}
		}, 'form');
		$('#form_unlock').validate(conf);
	});
	</script>
</div>
<div id="detail_money" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.add_money', 'id' => 'form_money']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td class="w108">补款金额:</td>
			<td>{!! Form::text('money', null, ['class' => 'small']) !!} 元</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>补款</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
		$(function () {
			var conf = util.validate_conf({
				submitHandler : function (form) {
					$(form).ajaxSubmit({
						success : util.splash
					});
				},
				rules : {
					money : {
						required : true,
						integer : true
					}
				}
			}, 'form');
			$('#form_money').validate(conf);
		})
	});
	</script>
</div>
<div id="detail_time" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.add_time', 'id' => 'form_time']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td class="w108">补时(小时):</td>
			<td>{!! Form::text('hour', null, ['class' => 'small']) !!} 小时</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>补时</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
		var conf = util.validate_conf({
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					success : util.splash
				});
			},
			rules : {
				time : {
					required : true,
					integer : true
				}
			}
		}, 'form');
		$('#form_time').validate(conf);
	});
	</script>
</div>
<div id="detail_game_pwd" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.game_pwd', 'id' => 'form_game_pwd']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
        <tr>
            <td class="w108">游戏密码:</td>
            <td>{!! Form::text('game_pwd', null, ['class' => 'small']) !!} </td>
        </tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>修改密码</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {

		var conf = util.validate_conf({
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					success : util.splash
				});
			},
			rules : {
				game_pwd : {
					required : true
				}
			}
		}, 'form');
		$('#form_game_pwd').validate(conf);
	});
	</script>
</div>
<div id="detail_cancel_apply" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.cancel', 'id'=> 'form_yi_pub_cancel']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	{!! Form::hidden('flag', \App\Lemon\Dailian\Application\Platform\Yi::PUB_CANCEL_APPLY) !!}
	<table class="table">
		<tr>
			<td class="w108">撤销标记:</td>
			<td>申请撤销</td>
		</tr>
		<tr>
			<td class="w108">说明</td>
			<td>
				<p class="mb5">
					{!! Form::label('order_price', '您已预付的代练费') !!} ¥ {!! $order->order_price !!} 元
				</p>
				<div class="form-group clearfix mb5 ">
					{!! Form::label('order_safe_money', '对方预付的安全保证金') !!} ¥ {!! $order->order_safe_money !!} 元
				</div>
				<div class="form-group clearfix mb5">
					{!! Form::label('order_speed_money', '对方预付的效率保证金') !!} ¥ {!! $order->order_speed_money !!} 元
				</div>
			</td>
		</tr>
		<tr>
			<td class="w108">支付代练费:</td>
			<td>{!! Form::text('pub_pay', null, ['class'=> 'w96']) !!} </td>
		</tr>
		<tr>
			<td class="w108">赔偿保证金:</td>
			<td>{!! Form::text('sd_pay', null, ['class'=> 'w96']) !!} </td>
		</tr>
		<tr>
			<td class="w108">说明:</td>
			<td>{!! Form::text('message', null) !!} </td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>订单撤销</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate','jquery.form'], function ($, util) {
		var conf = util.validate_conf({
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					success : util.splash
				});
			},
			rules : {
				sd_pay : {
					required : true,
					number : true,
					max : {!! $order->order_safe_money + $order->order_speed_money !!}
				},
				pub_pay : {
					required : true,
					number : true,
					max : {!! $order->order_price !!}
				},
				message : {
					required : true
				}
			}
		}, 'bt3');
		$('#form_yi_pub_cancel').validate(conf);
	});
	</script>
</div>
<div id="detail_sd_cancel_handle" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.cancel']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td class="w108">撤销标记:</td>
			<td>
				{!! Form::radios('flag', [
				\App\Lemon\Dailian\Application\Platform\Yi::PUB_CANCEL_AGREE => \App\Lemon\Dailian\Application\Platform\Yi::kvPubCancel(\App\Lemon\Dailian\Application\Platform\Yi::PUB_CANCEL_AGREE),
				], \App\Lemon\Dailian\Application\Platform\Yi::PUB_CANCEL_AGREE, ['desktop' => true]) !!}
			</td>
		</tr>
        <tr>
            <td></td>
        </tr>
		<tr>
			<td class="w108">说明</td>

			<td>
				<p class="mb5">
					{!! Form::label('order_price', '您已预付的代练费') !!} ¥ {!! $status->yi_order_price !!} 元
				</p>
				<div class="form-group clearfix mb5 ">
					{!! Form::label('order_safe_money', '需要支付的代练费') !!} ¥ {!! $status->yi_pub_pay !!} 元
				</div>
				<div class="form-group clearfix mb5">
					{!! Form::label('order_safe_money', '代练员支付的赔偿金') !!} ¥ {!! $status->yi_sd_pay !!} 元
				</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>操作</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
</div>
<div id="detail_cancel_cancel" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.cancel']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	{!! Form::hidden('flag', \App\Lemon\Dailian\Application\Platform\Yi::PUB_CANCEL_CANCEL) !!}
	<table class="table">
		<tr>
			<td class="w108">撤销标记:</td>
			<td>取消撤销</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>订单撤销</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
</div>
<div id="detail_star" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_yi.star', 'id' => 'form_star']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td class="w108">评价:</td>
			<td>{!! Form::radios('flag', \App\Lemon\Dailian\Application\Platform\Yi::kvStar(), null, [
			  'desktop' => true,
			  'inline' => false,
			]) !!} </td>
		</tr>
		<tr>
			<td class="w108">评价内容:</td>
			<td>{!! Form::text('message', null) !!} </td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>评价</span></button>
			</td>
		</tr>
	</table>
	{!! Form::close() !!}
	<script>
	require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
		var conf = util.validate_conf({
			submitHandler : function (form) {
				$(form).ajaxSubmit({
					success : util.splash
				});
			}
		}, 'form');
		$('#form_star').validate(conf);
	});
	</script>
</div>