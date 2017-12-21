@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page-dialog">
		@if ($accept)
			<div class="pd8">
				当前已经接单 : {!! $accept_account->note !!}
			</div>
		@endif
		@if ($un_accept_has_account)
			<table class="table">
				<tr>
					<th class="w72">ID</th>
					<th>用户名</th>
					{{--<th class="w72">QQ</th>--}}
					<th class="w216">
						操作
					</th>
				</tr>
				@foreach($employee_list as $item)
					<tr>
						<td>{!! $item->account_id !!}</td>
						<td>{!! $item->account_name !!}</td>
						{{--<td>{!! $item->qq !!}</td>--}}
						<td>
							{{--已经点击发布, 检测发布的状态--}}
{{--							@if (isset($platform_status[$item->account_id]))--}}
								{{--这里的数据来自于 blade(platform_order/index)--}}
								{{--平台重新发布--}}
								@if (!$accept)
									{{--@can('assign_publish', $status)  --}}
										<a href="{!! route('dsk_platform_employee.assign_employee', [$order->order_id, $item->account_id]) !!}" class="J_request">
											<i class="fa fa-share text-info" data-toggle="tooltip" title="发布订单"></i>
										</a>
									{{--@endcan--}}
								@endif
							{{--@else--}}
								{{--尚未点击发布时候的状态--}}
								{{--<a href="{!! route('dsk_platform_order.assign_publish', [$order->order_id, $item->id]) !!}" class="J_request">--}}
									{{--<i class="fa fa-share text-info" data-toggle="tooltip" title="发布到 {!! \App\Models\PlatformAccount::kvPlatform($item->platform) !!} 平台"></i>--}}
								{{--</a>--}}
								{{--@if (isset($un_accept_publish_reason[$item->id.'_error']) && $un_accept_publish_reason[$item->id.'_error'])--}}
									{{--<i class="fa fa-commenting-o text-danger"> {!! $un_accept_publish_reason[$item->id.'_error'] !!}</i>--}}
								{{--@endif--}}
							{{--@endif--}}
						</td>
					</tr>
				@endforeach
			</table>
		@else
			<div class="center-block flash-danger">
				<p>无绑定发单账号资料</p>
			</div>
		@endif

	</div>
@endsection