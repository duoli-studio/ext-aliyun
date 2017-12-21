@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    @include('desktop.platform_order.header_detail')
	<div class="page">
		<table class="table">
			<tr>
				<td class="w108">发单账号</td>
				<td>{!! $status->platformAccount->note !!}</td>
			</tr>
			@if (!$status->yi_is_delete)
				<tr>
					<td class="w108">订单信息</td>
					<td>
						<p><span class="inline-block w96">订单号: </span>{!! $status->yi_order_no !!}</p>
						@if ($status->yi_is_accept)
							<p class="mt3"><span class="inline-block w96">剩余时间(小时): </span>{!! $status->yi_left_hour !!}</p>
						@endif
						<p class="mt3"><span class="inline-block w96">订单状态: </span>{!! \App\Lemon\Dailian\Application\Platform\Yi::kvOrderStatus($status->yi_order_status) !!}</p>
						<p class="mt3">
							<span class="inline-block w96">是否公共: </span>{!! \App\Lemon\Dailian\Application\Platform\Yi::kvIsPublic($status->yi_is_public) !!}
						</p>
						@if ($status->yi_is_accept)
							<p class="mt3"><span class="inline-block w96">接手时间: </span>{!! $status->yi_assigned_at !!}</p>
							<p class="mt3"><span class="inline-block w96">结束时间: </span>{!! $status->yi_overed_at !!}</p>
						@endif
					</td>
				</tr>
				<tr>
					<td>发布者信息</td>
					<td>
						<p><span class="inline-block w96">发布者用户ID: </span>{!! $status->yi_pub_uid !!}</p>
						<p><span class="inline-block w96">发布者用户昵称: </span>{!! $status->yi_pub_username !!}</p>
						<p><span class="inline-block w96">发单联系人: </span>{!! $status->yi_pub_contact !!}</p>
						<p class="mt3"><span class="inline-block w96">发布时间: </span>{!! $status->yi_created_at !!}</p>
						<p class="mt3"><span class="inline-block w96">更新时间: </span>{!! $status->yi_updated_at !!}</p>
					</td>
				</tr>
				<tr>
					<td>接单者信息</td>
					<td>
						@if ($status->yi_is_accept)
							<p><span class="inline-block w96">接手者用户ID: </span>{!! $status->yi_sd_uid !!}</p>
							<p><span class="inline-block w96">接手者昵称: </span>{!! $status->yi_sd_username !!}</p>
							<p><span class="inline-block w96">接手者联系人: </span>{!! $status->yi_sd_contact !!}</p>
							<p><span class="inline-block w96">接手者QQ: </span>{!! $status->yi_sd_qq !!}</p>
							<p><span class="inline-block w96">接手者手机: </span>{!! $status->yi_sd_mobile !!}</p>
						@else
							尚未接单
						@endif
					</td>
				</tr>
			@endif
			@if ($status->yi_is_accept)
				<tr>
					<td>撤销状态</td>
					<td>
						{!! \App\Lemon\Dailian\Application\Platform\Yi::kvCancelStatus($status->yi_cancel_status) !!}
					</td>
				</tr>
				<tr>
					<td>锁定状态</td>
					<td>
						{!! \App\Lemon\Dailian\Application\Platform\Yi::kvLock($status->yi_is_lock) !!}
					</td>
				</tr>
			@endif
			@if ($status->yi_is_over)
				<tr>
					<td>是否评价</td>
					<td>
						{!! \App\Lemon\Dailian\Application\Platform\Yi::kvStar($status->yi_is_star) !!}
					</td>
				</tr>
			@endif
			<tr>
				<td>当前发布状态</td>
				<td>
					@if($status->yi_is_publish)
						<p>
							<i class="fa fa-paper-plane  text-info"> {!! \App\Models\PlatformStatus::kvIsPublish($status->yi_is_publish) !!}</i>
						</p>
					@endif
					@if ($status->yi_is_delete)
						<p class="mt8">
							<i class="fa fa-close text-danger"> {!! \App\Models\PlatformStatus::kvIsDelete($status->yi_is_delete) !!}</i>
						</p>
					@endif
					<p class="mt8">
						<i class="fa fa-comment-o  {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_YQ) !!}">{!! $status->yi_pt_message !!}</i>
					</p>
				</td>
			</tr>
		</table>
	</div>
@endsection