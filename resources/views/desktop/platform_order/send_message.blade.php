@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
		{!! Form::open(['url' => route('dsk_platform_order.send_message', [$order->order_id]), 'id' => 'message_send']) !!}
		<table class="table">
            <tr>
                <td>选择模板: {!! Form::select('tpl_id', $sms_tpl, null, ['placeholder' => '所有']) !!}</td>
            </tr>
			<tr>
				<td colspan="2">
					短信内容: <br>
					{!! Form::textarea('message_content', null, ['style' => 'height: 60px;']) !!}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{!! Form::button('发送', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
				</td>
			</tr>
		</table>
		{!! Form::close() !!}
		<script>
		require(['jquery', 'lemon/util', 'jquery.validate'], function ($, util) {
			var conf = util.validate_conf({}, 'dsk_ajax');
			$('#message_send').validate(conf);
		});
		</script>
@endsection