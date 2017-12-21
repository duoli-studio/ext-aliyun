@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['url' => route('dsk_platform_order.handle_question', [$order->order_id]), 'id' => 'form_talk']) !!}
    <table class="table">
        <tr>
            <td class="w72">订单编号:</td>
            <td>{!! $order->order_get_in_number !!}</td>
        </tr>
        <tr>
            <td>问题类型:</td>
            <td>{!! $order->question_type !!}</td>
        </tr>
        <tr>
            <td>问题描述:</td>
            <td>{!! $order->question_description !!}</td>
        </tr>
        <tr>
            <td>图片:</td>
            <td>{!! Form::showThumb($order->question_thumb, ['width'=> '100']) !!}</td>
        </tr>
        <tr>
            <td>处理人:</td>
            <td>
                @if ($order->questionHandleAccount)
                    {!! $order->questionHandleAccount->account_name !!}
                @else
                    未指定处理人
                @endif
            </td>
        </tr>
        <tr>
            <td>提交人:</td>
            <td>
                {!! $order->questionAccount->account_name !!}
            </td>
        </tr>
        <tr>
            <td>问题状态:</td>
            <td>{!! Form::select('question_status', \App\Models\PlatformOrder::kvQuestionStatus(), $order->question_status) !!}</td>
        </tr>
        <tr>
            <td colspan="2">
                {!! Form::textarea('message', null, ['style' => 'height: 60px;width:250px', 'class'=> 'form-control']) !!}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                {!! Form::button('提交处理', ['class'=> 'btn btn-primary mb8', 'type'=> 'submit']) !!}
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
	require(['jquery', 'lemon/util', 'jquery.validate'], function ($, util) {
		var conf = util.validate_conf({}, 'dsk_ajax');
		$('#form_talk').validate(conf);
	});
    </script>
@endsection