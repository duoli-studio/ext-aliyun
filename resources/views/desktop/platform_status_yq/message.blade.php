@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page">
		@include('desktop.platform_order.header_detail')
		<div>
			<table class="table">
				<tr>
					<td>
						<div style="height: 500px;overflow-y: auto;width:570px;">
							@foreach($messages as $message)
								<dl>
									<dt class="pt3">
										<i class="fa fa-user"> {!! $message['nickname'] !!} </i> &nbsp;&nbsp;
										<i class="fa fa-clock-o"> {!! $message['create_time'] !!}</i> &nbsp;&nbsp;
									</dt>
									<dd class="pd5 pb8">
                                        {!! $message['type_msg'] !!}
                                        {!! $message['comment'] !!}
									</dd>
								</dl>
							@endforeach
						</div>
					</td>u
				</tr>
				<tr>
					<td>
						{!! Form::open(['route' => 'dsk_platform_status_yq.message', 'id' => 'form_message']) !!}
						{!! Form::hidden('status_id', $status->id) !!}
						{!! Form::textarea('reason', null) !!}
						<button class=" btn btn-primary">
							<span>提交留言</span>
						</button>
						{!! Form::close() !!}
					</td>
				</tr>
			</table>

			<script>
			require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
				var conf = util.validate_conf({
					submitHandler : function (form) {
						$(form).ajaxSubmit({
							success : util.splash
						});
					},
					rules : {
						reason : {
							required : true
						}
					}
				}, 'bt3_inline');
				$('#form_message').validate(conf);
			});
			</script>
		</div>
	</div>
@endsection