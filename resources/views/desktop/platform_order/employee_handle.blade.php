@can('employee_pub_cancel_apply', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_cancel_apply" data-title="撤销">
		<span> 申请撤销 </span>
	</button>
@endcan
@can('employee_pub_cancel_cancel', $status)
	<a class=" btn btn-primary btn-sm mr3 J_request text-info" data-confirm="确认取消撤单?" data-title="取消撤销"
       href="{!! route('dsk_platform_employee.cancel_cancel', [$order_id,$status->id]) !!}">
		<span> 取消撤销</span>
	</a>
@endcan
@can('employee_ing', $status)
	<button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_game_pwd" data-title="修改密码"><span>修改游戏密码</span></button>
@endcan
@can('employee_progress', $status)
	<a class=" btn btn-primary btn-sm mr3 J_iframe text-info" data-title="更新进度"
       href="{{ route('dsk_platform_employee.update_progress', [$order_id]) }}">
        <span>更新进度</span>
    </a>
@endcan
@can('employee_over', $status)
	<a class=" btn btn-primary btn-sm mr3 J_request text-danger " data-confirm="确认完成订单?" href="{!! route('dsk_platform_employee.confirm_order_over', [$order_id,$status->id]) !!}">
		<span> 确认完单</span>
	</a>
@endcan
<div id="detail_game_pwd" class="hidden">
	{!! Form::open(['route' => 'dsk_platform_status_employee.game_pwd', 'id' => 'form_game_pwd']) !!}
	{!! Form::hidden('order_id', $order->order_id) !!}
	<table class="table">
		<tr>
			<td class="w108">游戏账号:</td>
			<td>{!! Form::text('game_account', $order->game_account, ['class' => 'small']) !!} </td>
		</tr>
        <tr>
            <td class="w108">游戏密码:</td>
            <td>{!! Form::text('game_pwd', $order->game_pwd, ['class' => 'small']) !!} </td>
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
	{!! Form::open(['route' => 'dsk_platform_status_employee.cancel', 'id'=> 'form_yi_pub_cancel']) !!}
	{!! Form::hidden('status_id', $status->id) !!}
	<table class="table">
		<tr>
			<td class="w108">撤销标记:</td>
			<td>申请撤销</td>
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
	require(['jquery', 'lemon/util', 'jquery.validate', '1dailian/front_cp', 'jquery.form'], function ($, util) {
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