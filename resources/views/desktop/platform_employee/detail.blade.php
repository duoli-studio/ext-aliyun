@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page">
        @include('desktop.platform_employee.header_button')
		<div class="clearfix">
			@include('desktop.platform_employee.header_detail')
		</div>
		<div class="clearfix pl5 pt7 pb3 pr20">
			<div class="fl">
				当前状态: <span class="text-info">{!! \App\Models\PlatformOrder::kvOrderStatus($order->order_status) !!}</span>
				@if($order->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
					@if ($order->cancel_type == \App\Models\PlatformOrder::CANCEL_TYPE_KF)
						{{ \App\Models\PlatformOrder::kvKfStatus($order->kf_status) }}
					@else
						{!! \App\Models\PlatformOrder::kvCancelStatus($order->cancel_status) !!}
					@endif
				@endif
			</div>
		</div>
        @include('desktop.inc.pt_status_detail')
	</div>
	<script>
	require(['jquery', 'lemon/util', 'jquery.form', 'jquery.validate'], function ($, util) {
		// 更新进度
		var conf = util.validate_conf({
			rules : {
				'content' : {required : true}
			}
		});
		$('#form_progress').validate(conf);

		// this is the id of the form
		$('#btn_save').on('click', function (e) {
			// the script where you handle the form input.
			var url = "{!! route('dsk_platform_order.update', [$order->order_id]) !!}";
			$.post(url,
				$("#form_order").serialize(), // serializes the form's elements.
				util.splash);
			e.preventDefault(); // avoid to execute the actual submit of the form.
		})
	})
	</script>

@endsection