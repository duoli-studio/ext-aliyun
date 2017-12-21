@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['url' => route('dsk_platform_order.question', [$order->order_id]), 'id' => 'form_question']) !!}
    <table class="table">
        <tr >
            <td colspan="2">订单编号: {!! $order->order_get_in_number !!}</td>
        </tr>
        <tr>
            <td>
                问题类型: {!! Form::select('question_type', \App\Models\PlatformOrder::kvQuestionType(), null, ['placeholder' => '请选择问题类型']) !!}</td>
            <td>
                处理人: {!! Form::select('question_handle_account_id', $handle_account, null, ['placeholder' => '所有']) !!}</td>
        </tr>
        <tr>
            <td colspan="2">
                问题描述: <br>
                {!! Form::textarea('question_description', null, ['style' => 'height: 60px;', 'class'=> 'form-control']) !!}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="fl pt5"> 图片上传 &nbsp;</div>
                <div class="fl">
                    {!! Form::thumb('question_thumb', null) !!}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                {!! Form::button('提交问题', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
		require(['jquery', 'lemon/util', 'jquery.validate'], function ($, util) {
			var conf = util.validate_conf({}, 'dsk_ajax');
			$('#form_question').validate(conf);
		});
    </script>
@endsection