@extends('lemon.template.desktop_angle')
@section('desktop-main')
		@include('desktop.plugin_ip.header')
        <div class="panel panel-default">
            <div class="panel-body">
			<!-- 数据表格 -->
			<table class="table">
				<tr >
					<th class="w72">ID</th>
					<th class="w360">IP</th>
					<th>备注</th>
					<th class="w108">操作</th>
				</tr>
				@foreach($ips as $ip)
				<tr>
					<td>{{$ip['ip_id']}}</td>
					<td><span class="J_editIp" id="J_editIp_addr_{{$ip['ip_id']}}">{{$ip['ip_addr']}}</span></td>
					<td><span class="J_editNote" id="J_editNote_{{$ip['ip_id']}}">{{$ip['note']}}</span></td>
					<td>
						<a class="fa fa-remove fa-lg text-danger J_request" data-confirm="确认删除 ? " data-toggle="tooltip" title="删除" href="{{route('dsk_plugin_ip.destroy', [$ip['ip_id']])}}"></a>
					</td>
				</tr>
				@endforeach
			</table>
			<!-- 分页 -->
			<div class="clearfix mt10">
				{!! $ips->render() !!}
			</div>
		</div>
	</div>
	<script>
	require(['jquery', 'lemon/util', 'jquery.editable'], function ($, util) {
		$('.J_editIp').editable("{{route('dsk_plugin_ip.ip')}}", {
			callback : function (value, setting) {
				var obj = $.parseJSON(value);
				util.splash(obj);
				$(this).html(obj.value);
			},
			type : 'ipv4'
		});
		$('.J_editNote').editable("{{route('dsk_plugin_ip.note')}}", {
			callback : function (value, setting) {
				var obj = $.parseJSON(value);
				util.splash(obj);
				$(this).html(obj.value);
			},
			type : 'require'
		});
	})
	</script>
@endsection