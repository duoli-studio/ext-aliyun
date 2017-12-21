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
                                        {!! Form::showThumb(isset($message['touaddr']) ? $message['touaddr'] :'' ,['height'=>'30','width'=>'30']) !!}
                                        {!! isset($message['nickname']) ? $message['nickname'] :'' !!}&nbsp;
                                        <i class="fa fa-clock-o"> {!! isset($message['createtime']) ? $message['createtime'] :'' !!}</i> &nbsp;&nbsp;
                                    </dt>
                                    <dd class="pd5 pb8">
                                        {!! \App\Models\PlatformMaoLog::kvMsgType(isset($message['type']) ?$message['type'] : App\Models\PlatformMaoLog::MSG_SYSTEM_OPERATION) !!}:
                                        {!! isset($message['content']) ? $message['content'] : '' !!}
                                    </dd>
                                </dl>
                            @endforeach
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{!! route_url('dsk_platform_status_mama.message_next',null, ['status_id' =>$status->id,'begin_id'=>$begin_id]) !!}">
                            下一頁
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        {!! Form::open(['route' => 'dsk_platform_status_mama.message', 'id' => 'form_message']) !!}
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
						submitHandler: function (form) {
							$(form).ajaxSubmit({
								success: util.splash
							});
						},
						rules        : {
							reason: {
								required: true
							}
						}
					}, 'bt3_inline');
					$('#form_message').validate(conf);
				});
            </script>
        </div>
    </div>
@endsection