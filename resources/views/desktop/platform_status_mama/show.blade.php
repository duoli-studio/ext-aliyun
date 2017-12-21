@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page">
        @include('desktop.platform_order.header_detail')
		<table class="table">
			<tr>
				<td>发单账号</td>
				<td>{!! $status->platformAccount->note !!}</td>
			</tr>
			<tr>
				<td>代练猫订单号:</td>
				<td>{!! $status->mama_order_no !!}</td>
			</tr>
			<tr>
				<td>接单人信息:</td>
				<td>
					<i class="fa fa-info-circle grey"> 说明: 这里接单之后才显示接单人的信息 </i>
					<p><span class="inline-block w96">接单人qq: </span>{!! $status->mama_sd_qq !!}</p>
					<p class="mt3"><span class="inline-block w96">接单人ID: </span>{!! $status->mama_sd_uid !!}</p>
					<p class="mt3"><span class="inline-block w96">接单人昵称: </span>{!! $status->mama_sd_name !!}</p>
					<p class="mt3"><span class="inline-block w96">接单人联系电话: </span>{!! $status->mama_sd_mobile !!}</p>
				</td>
			</tr>
			<tr>
				<td>接单状态:</td>
				<td>{!! $status->mama_status_desc !!}</td>
			</tr>
			<tr>
				<td>订单信息:</td>
				<td>
					<p><span class="inline-block w96">订单时限: </span>{!! $status->mama_order_hour !!}</p>
					<p class="mt3"><span class="inline-block w96">剩余时间: </span>{!! $status->mama_left_hour !!}</p>
					<p class="mt3"><span class="inline-block w96">关闭天数: </span>{!! $status->mama_close_day !!}</p>
					<p class="mt3"><span class="inline-block w96">接单时间: </span>{!! $status->mama_started_at !!}</p>
					<p class="mt3"><span class="inline-block w96">订单创建时间: </span>{!! $status->mama_created_at !!}</p>
					<p class="mt3"><span class="inline-block w96">最后修改时间: </span>{!! $status->mama_updated_at !!}</p>
				</td>
			</tr>
			<tr>
				<td>当前发布状态</td>
				<td>
					@if($status->mama_is_publish)
						<p>
							<i class="fa fa-paper-plane  text-info"> {!! \App\Models\PlatformStatus::kvIsPublish($status->mama_is_publish) !!}</i>
						</p>
					@endif
					@if ($status->mama_is_delete)
						<p class="mt8">
							<i class="fa fa-close text-danger"> {!! \App\Models\PlatformStatus::kvIsDelete($status->mama_is_delete) !!}</i>
						</p>
					@endif
					<p class="mt8"><i class="fa fa-comment-o  {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_MAO) !!}"> {!! $status->mama_pt_message !!}</i></p>
				</td>
			</tr>
		</table>
	</div>
@endsection