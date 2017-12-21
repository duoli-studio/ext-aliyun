@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    @include('desktop.platform_order.header_detail')
    <!--3 代表已结算完成 -->
    {!! Form::model($order, ['url' => route('dsk_platform_order.get_in', [$order->order_id]), 'id' => 'form_get_in','class'=>'form-horizontal']) !!}
    <div class="form-group">
        {!! Form::label('source_id', trans('订单来源:'), ['class' => 'col-md-2 control-label']) !!}
        <div class="col-md-4">
            {!! Form::select('source_id', \App\Models\GameSource::kvSourceTitle(), null, ['placeholder'=> '请选择订单来源', 'class'=> 'form-control ']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('order_get_in_number', trans('订单号:'), ['class' => 'col-md-2 control-label']) !!}
        <div class="col-md-4">
            {!! Form::text('order_get_in_number',null,['class'=> 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('J_order_tags', trans('检索标签:'), ['class' => 'col-md-2 control-label']) !!}
        <div class="col-md-4">
            <div id="J_order_tags">
            @if (isset($order_tags) && $order_tags){!! $order_tags !!}@endif
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('order_note', trans('备注:'), ['class' => 'col-md-2 control-label']) !!}
        <div class="col-md-4">
            {!! Form::textarea('order_note', null, ['class'=> 'form-control ','rows' =>'3']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('', '', ['class' => 'col-md-2 control-label']) !!}
        <div class="col-md-4">
            {!! Form::button('保存', ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    <script>
		require(['jquery', 'lemon/util', 'jquery.form', 'jquery.validate', 'jquery.tagging-js'], function ($, util) {
			$(function () {
				$('#J_order_tags').tagging({
					'tags-input-name': 'order_tags',
					'tag-box-class'  : 'sj-tagging'
				});
			});
			var conf = util.validate_conf({
				rules: {}
			});
			$('#form_get_in').validate(conf);
		})
    </script>
@endsection