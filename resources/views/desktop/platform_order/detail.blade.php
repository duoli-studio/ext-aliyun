@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    <style>
        .pagination ul {
            margin-top    : 0;
            margin-bottom : 0;
        }
        .pagination-sm .pagination li > a, .pagination-sm .pagination > li > span {
            padding   : 5px 10px;
            font-size : 12px;
        }

    </style>
    <ul class="nav nav-tabs">
        @foreach($rel_ids as $__id)
            <li @if ($order_id == $__id) class="active" @endif>
                <a href="{!! route_url('dsk_platform_order.detail', $__id) !!}">订单 {!! $__id !!}</a>
            </li>
        @endforeach
    </ul>
    <div class="btn-group-sm form-group-sm mt8">
        {{--这个地方防止第三方--}}
        {!! Form::button('保存', ['class'=> 'btn btn-primary btn-sm', 'id'=> 'btn_save']) !!}
        @can('reload', $order)
            {{--            {{dd($order)}}--}}
            @if ($order->accept_platform != \App\Models\PlatformAccount::Employee)
                <a href="{!! route('dsk_platform_order.reload', [$order->order_id]) !!}"
                   class="J_request btn btn-primary btn-sm mr3">
                    <i class="fa fa-refresh " data-placement="bottom"
                       data-toggle="tooltip" title="同步所有平台订单信息, 如果全部删单, 也需要操作此按钮进行订单重新回滚操作!">
                        同步
                    </i>
                </a>
            @endif
        @endcan
        @can('refund', $order)
            @if ($order->accept_platform != \App\Models\PlatformAccount::Employee)
                <a href="{!! route('dsk_platform_order.refund', [$order->order_id]) !!}" data-confirm="确认要对订单进行退款吗?"
                   class="J_request btn btn-danger btn-sm mr3" data-placement="bottom" data-toggle="tooltip"
                   title="订单退款!">
                    订单退款
                </a>
            @endif
        @endcan
        @if (isset($order->accept_platform))
            @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_MAO)
                @include('desktop.platform_order.mao_handle')
            @elseif ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_TONG)
                @include('desktop.platform_order.tong_handle')
            @elseif ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_MAMA)
                @include('desktop.platform_order.mama_handle')
            @elseif ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_YI)
                @include('desktop.platform_order.yi_handle')
            @elseif ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_BAOZI)
                @include('desktop.platform_order.baozi_handle')
            @elseif ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_YQ)
                @include('desktop.platform_order.yq_handle')
            @elseif ($order->accept_platform == \App\Models\PlatformAccount::Employee)
                @include('desktop.platform_order.employee_handle')
            @else
                {!! Form::button('锁定', ['class'=> 'btn btn-default', 'disabled', 'id'=> 'btn_lock']) !!}
                {!! Form::button('撤销', ['class'=> 'btn btn-default', 'disabled','id'=> 'btn_quash']) !!}
                {!! Form::button('订单退款', ['class'=> 'btn btn-default','disabled', 'id'=> 'btn_cancel']) !!}
                {!! Form::button('更新进度', ['class'=> 'btn btn-default','disabled', 'id'=> 'btn_progress']) !!}
                {!! Form::button('确认完单', ['class'=> 'btn btn-default','disabled', 'id'=> 'btn_over']) !!}
            @endif
        @endif
    </div>

    <div class="clearfix">
        @include('desktop.platform_order.header_detail')
    </div>

    <div class="row  form-group-sm">
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">平台订单</div>
                <div class="panel-body">
                    <div class="clearfix mb8">
                        <div class="fl">
                            当前状态: <span
                                    class="blue">{!! \App\Models\PlatformOrder::kvOrderStatus($order->order_status) !!}</span>
                            @if($order->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                @if ($order->cancel_type == \App\Models\PlatformOrder::CANCEL_TYPE_KF)
                                    {{ \App\Models\PlatformOrder::kvKfStatus($order->kf_status) }}
                                @else
                                    {!! \App\Models\PlatformOrder::kvCancelStatus($order->cancel_status) !!}
                                @endif
                            @endif
                        </div>
                        <div class="fr">
                            色彩标注 :
                            <a href="{!! route('dsk_platform_order.color', [$order->order_id, 'brown']) !!}"
                               data-toggle="tooltip" title="棕色"
                               class="J_request mark-color mark-color-brown">&nbsp;</a>
                            <a href="{!! route('dsk_platform_order.color', [$order->order_id, 'green']) !!}"
                               data-toggle="tooltip" title="绿色"
                               class="J_request mark-color mark-color-green">&nbsp;</a>
                            <a href="{!! route('dsk_platform_order.color', [$order->order_id, 'blue']) !!}"
                               data-toggle="tooltip" title="蓝色"
                               class="J_request mark-color mark-color-blue">&nbsp;</a>
                            <a href="{!! route('dsk_platform_order.color', [$order->order_id, 'red']) !!}"
                               data-toggle="tooltip" title="红色"
                               class="J_request mark-color mark-color-red">&nbsp;</a>
                            <a href="{!! route('dsk_platform_order.color', [$order->order_id, 'cancel']) !!}"
                               data-toggle="tooltip" title="取消标注"
                               class="J_request mark-color mark-color-cancel">&nbsp;</a>
                        </div>
                    </div>

                    {!! Form::model(isset($order) ? $order : null, ['url'=> route('dsk_platform_order.update', [$order->order_id]), 'id' => 'form_order','class'=>'form-horizontal']) !!}
                    <div class="form-group">
                        <label class="col-md-2 control-label">接单价格</label>
                        <div class="col-md-4">
                            {!! Form::text('order_get_in_price', null, ['class'=>'form-control' ]) !!}
                        </div>

                        <label class="col-md-2 control-label">发单价格</label>
                        <div class="col-md-4">
                            {!! Form::text('order_price', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">效率金</label>
                        <div class="col-md-4">
                            {!! Form::text('order_speed_money', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                        </div>
                        <label class="col-md-2 control-label">安全金</label>
                        <div class="col-md-4">
                            {!! Form::text('order_safe_money', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">订单耗时</label>
                        <div class="col-md-4">
                            {!! Form::text('order_hours', null, ['class'=> 'form-control']) !!}
                        </div>
                        <label class="col-md-2 control-label">代练时限</label>
                        <div class="col-md-4">
                            {!! Form::text('order_hours', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">订单标题</label>
                        <div class="col-md-10">
                            {!! Form::text('order_title', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">游戏区服</label>
                        <div class="col-md-4">
                            {!! Form::text('game_area', null, ['class'=> 'form-control', 'disabled']) !!}
                        </div>
                        <label class="col-md-2 control-label">角色名</label>
                        <div class="col-md-4">
                            {!! Form::text('game_actor', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">游戏账号</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('game_account', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                                <div class="input-group-addon">
                                    <a href="tencent://message/?uin={{$order->game_account}}">
                                        <img height="16" border="0" src="/assets/images/fa/qq.png" alt="联系号主"/></a>
                                </div>
                            </div>
                        </div>
                        <label class="col-md-2 control-label">号主旺旺</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                {!! Form::text('order_get_in_wangwang', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                                <div class="input-group-addon">
                                    <a target="_blank"
                                       href="http://www.taobao.com/webww/ww.php?ver=3&touid={{$order->order_get_in_wangwang}}&siteid=cntaobao&status=2&charset=utf-8"><img
                                                border="0"
                                                src="http://amos.alicdn.com/online.aw?v=2&uid={{$order->order_get_in_wangwang}}&site=cntaobao&s=2&charset=utf-8"
                                                alt="联系号主"/></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">联系方式</label>
                        <div class="col-md-2 pt8">
                            @if ($order->order_get_in_mobile)
                                <a data-width="300" data-height="100" data-shade_close="true"
                                   href="{!! route('dsk_platform_order.show_field', [$order->order_id, 'order_get_in_mobile']) !!}"
                                   class="J_iframe label label-primary">
                                    查看
                                </a>
                            @else
                                --无--
                            @endif
                        </div>
                        <div class="col-md-7 pt8">
                            <a data-width="850" data-height="750" data-shade_close="true"
                               href="{!! route('support_tgp.player', [$order->order_id, 'zj']) !!}"
                               class="label label-primary J_iframe">战绩</a>

                            <a data-width="300" data-height="100" data-shade_close="true"
                               href="{!! route('dsk_platform_order.show_field', [$order->order_id, 'game_pwd']) !!}"
                               class="label label-primary J_iframe mb5">查看密码</a>

                            @if ($can_edit)
                                <a data-width="300" data-height="200" data-shade_close="true"
                                   data-element="#detail_change_pwd"
                                   class="label label-primary J_dialog mt5">修改密码 </a> &nbsp;
                            @endif
                            @if($order->enclosure)
                                <a href="{!! $order->enclosure !!}"
                                   class="label label-primary">下载附件</a>
                            @endif
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    平台留言
                    <div class="pull-right">
                        @if ($progress->hasPages())
                            <div class="pagination mt0 mb0 clearfix pagination-sm">{!! $progress->render() !!}</div>
                        @endif
                    </div>
                </div>
                <div class="panel-body">
                    @if ($progress->total())
                        <table class="table table-narrow" width="100%">
                            <tr>
                                <th>ID</th>
                                <th class="w96">操作时间</th>
                                <th class="w72">操作员</th>
                                <th>说明</th>
                            </tr>
                            @foreach($progress as $k => $p)
                                <tr>
                                    <td>{!! $p->log_id !!}</td>
                                    <td>{!! \App\Lemon\Repositories\Sour\LmTime::datetime($p->created_at, '2-2') !!}</td>
                                    <td>
                                        {!! \App\Models\PamAccount::accountTypeDesc($p->log_by) !!}
                                        @if ($p->account_id && $p->pam)
                                            [{!! $p->pam->account_name !!}]
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>{!! $p->log_content !!}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        @include('desktop.inc.empty')
                    @endif
                    {!! Form::open(['url' => route('dsk_platform_order.progress', [$order->order_id]), 'id' => 'form_progress', 'method' => 'post']) !!}
                    <table class="table">
                        <tr>
                            <td>
                                {!! Form::textarea('message', null, ['style' => 'height: 60px;width:260px;', 'class'=>'form-control']) !!}
                            </td>
                            <td class="w84">
                                {!! Form::button('发送', ['class'=> 'btn btn-primary btn-sm', 'type'=> 'submit']) !!}
                            </td>
                        </tr>
                    </table>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="btn-group-sm form-group-sm">
        <div class="panel panel-default">
            <div class="panel-heading">

                基本信息
                <div class="pull-right">
                    {!! Form::button('保存', ['class'=> 'btn btn-primary btn-sm', 'id'=> 'btn_save_basic']) !!}
                </div>
            </div>
            <div class="panel-body">
                {!! Form::model(isset($order) ? $order : null, ['url'=> route('dsk_platform_order.update', [$order->order_id]), 'id' => 'form_order_basic','class'=>'form-horizontal']) !!}
                <div class="form-group">
                    <label class="col-md-2 control-label">订单编号</label>
                    <div class="col-md-2">
                        {!! Form::text('order_get_in_number', null, [$edit_disabled, 'class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">录单时间</label>
                    <div class="col-md-2">
                        {!! Form::text('created_at', null, ['disabled'=>'disabled', 'class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">发布时间</label>
                    <div class="col-md-2">
                        {!! Form::text('published_at', null, ['disabled'=>'disabled', 'class'=> 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">转单费用[-]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_zhuandan', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">合作方</label>
                    <div class="col-md-2">
                        {!! Form::text('accept_platform',
                            $order->accept_platform ? \App\Models\PlatformAccount::kvPlatform($order->accept_platform) : '--',
                            ['disabled'=>'disabled', 'class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">来源</label>
                    <div class="col-md-2">
                        {!! Form::text('source_title',null,['disabled'=>'disabled', 'class'=> 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">号主补分加钱[+]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_pub_bufen', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">代练补分加钱[-]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_sd_bufen', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">滞留订单时长</label>
                    <div class="col-md-2">
                        {!! Form::text('published_at', null, ['disabled'=>'disabled', 'class'=> 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">代练坏单赔偿[+]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_sd_huaidan', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">补偿号主费用[-]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_pub_buchang', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">订单其他费用[-]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_other', null, ['class'=> 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">代练坏单赔偿[+]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_sd_huaidan', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">补偿号主费用[-]</label>
                    <div class="col-md-2">
                        {!! Form::text('fee_pub_buchang', null, ['class'=> 'form-control']) !!}
                    </div>
                    <label class="col-md-2 control-label">订单进度</label>
                    <div class="col-md-2">
                        {!! Form::text('last_log', null, ['class'=> 'form-control']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script>
	require(['jquery', 'lemon/util', 'jquery.form', 'jquery.validate'], function ($, util) {
		// 更新进度
		var conf = util.validate_conf({
			rules: {
				'content': {required: true}
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
		$('#btn_save_basic').on('click', function (e) {
			// the script where you handle the form input.
			var url = "{!! route('dsk_platform_order.update', [$order->order_id]) !!}";
			$.post(url,
				$("#form_order_basic").serialize(), // serializes the form's elements.
				util.splash);
			e.preventDefault(); // avoid to execute the actual submit of the form.
		})

	})
    </script>
    <div id="detail_change_pwd" class="hide">
        {!! Form::open(['route' => ['dsk_platform_order.change_pwd', $order->order_id]]) !!}
        <table class="table">
            <tr>
                <td>密码:</td>
                <td>{!! Form::text('pwd', null, ['class'=> 'w120']) !!} </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <button class=" btn btn-primary J_submit"><span>修改密码</span></button>
                </td>
            </tr>
        </table>
        {!! Form::close() !!}
    </div>
@endsection