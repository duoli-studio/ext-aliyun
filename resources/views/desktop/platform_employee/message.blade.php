@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page">
        @include('desktop.platform_employee.header_button')
        <div class="clearfix">
            @include('desktop.platform_employee.header_detail')
        </div>
		<div>
			<table class="table">
				<tr>
					<td>
						<div style="height: 300px;overflow-y: auto;width:570px;">
							@foreach($messages as $message)
								<dl>
									<dt class="pt3">
										<i class="fa fa-user"> {!! $message['account_name'] !!} </i> &nbsp;&nbsp;
										<i class="fa fa-clock-o"> {!! $message['created_at'] !!}</i> &nbsp;&nbsp;
									</dt>
									<dd class="pd5 pb8">
										{!! $message['log_content'] !!}
									</dd>
								</dl>
							@endforeach
						</div>
					</td>
				</tr>
            </table>
                {!! Form::open(['route' => 'dsk_platform_employee.message', 'id' => 'form_message','class'=>'form-horizontal']) !!}
                {!! Form::hidden('order_id', $order_id) !!}
                <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-4">
                        {!! Form::textarea('reason', null,['rows' => '4','class'=>'mt1 mb5']) !!}
                    </div>
                    <div class="col-md-2">
                        <button class=" btn btn-info">
                            <span>提交留言</span>
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
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