<?php $order = isset($order) ? $order : $status->platformOrder; ?>
<table class="table">
    <tr class="border">
        <td class="w108">帐号信息</td>
        <td>
            <table class="table table-compact">
                <tr class="noborder">
                    <td class="w96">游戏账号:</td>
                    @if($order->order_status == \App\Models\PlatformOrder::ORDER_STATUS_PUBLISH)
                        <td>接单之后查看账号</td>
                    @else
                        <td>{!! $order->game_account !!}</td>
                    @endif
                </tr>
                <tr class="noborder">
                    <td>游戏密码:</td>
                    @if($order->order_status == \App\Models\PlatformOrder::ORDER_STATUS_PUBLISH)
                        <td>接单之后查看账号</td>
                    @else
                        <td>{!! $order->game_pwd !!}</td>
                    @endif
                </tr>
                <tr class="noborder">
                    <td>角色名:</td>
                    <td>{!! $order->game_actor !!}</td>
                </tr>
                <tr class="noborder">
                    <td>游戏区服:</td>
                    <td>{!! $order->game_area !!} ({!! $order->type_title !!})</td>
                </tr>
            </table>
        </td>
    </tr>
	<tr class="border">
		<td class="w108">订单信息</td>
		<td>
			<table class="table table-compact">
				<tr class="noborder">
					<td class="w96">订单标题:</td>
					<td>{!! $order->order_title !!}</td>
				</tr>

				<tr class="noborder">
					<td>订单价格:</td>
					<td>{!! $order->order_price !!}</td>
				</tr>
				<tr class="noborder">
					<td>安全保证金:</td>
					<td>{!! $order->order_safe_money !!}</td>
				</tr>
				<tr class="noborder">
					<td>效率保证金:</td>
					<td>{!! $order->order_speed_money !!}</td>
				</tr>
				<tr class="noborder">
					<td>代练时限:</td>
					<td>{!! $order->order_hours !!}</td>
				</tr>
				<tr class="noborder">
					<td>当前游戏信息:</td>
					<td>{!! $order->order_current !!}</td>
				</tr>
				<tr class="noborder">
					<td>代练内容:</td>
					<td>{!! $order->order_content !!}</td>
				</tr>

			</table>
		</td>
	</tr>

</table>