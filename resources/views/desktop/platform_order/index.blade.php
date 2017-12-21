@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_order.header')
    <div class="panel panel-default">
        <div class="panel-body text-sm">
            <ul class="nav nav-tabs">
                <li @if (!\Input::get('order_status')) class="active" @endif>
                    <a href="{!! route_url() !!}">
                        全部订单
                    </a>
                </li>
                @foreach(\App\Models\PlatformOrder::kvOrderStatus() as $os => $os_desc)
                    @if($os == \App\Models\PlatformOrder::ORDER_STATUS_ING)
                        {{--代练中--}}
                        <li class="@if (\Input::get('is_employee')) active @endif">
                            <a href="{!! route_url('', null, ['order_status'=>'ing', 'is_employee'=> 1]) !!}">
                                代练中-员工({!! isset($order_ing_nums[1]) ? $order_ing_nums[1] : 0 !!})
                            </a>
                        </li>
                        <li class="@if (\Input::get('is_publish')) active @endif">
                            <a href="{!! route_url('', null, ['order_status'=>'ing', 'is_publish'=> 1]) !!}">
                                代练中-发单({!! isset($order_ing_nums[0]) ? $order_ing_nums[0] : 0 !!})
                            </a>
                        </li>
                    @elseif ($os == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                        <li @if (\Input::get('order_status') == $os) class="active" @endif>
                            <a href="{!! route_url('', null, ['order_status'=>'cancel']) !!}">
                                撤销中 ({!! $order_cancel_num !!})
                            </a>
                        </li>
                    @elseif ($os == \App\Models\PlatformOrder::ORDER_STATUS_QUASH)
                        <li @if (\Input::get('order_status') == $os) class="active" @endif>
                            <a href="{!! route_url('', null, ['order_status'=>'quash']) !!}">
                                撤销完成 ({!! $order_quash_num !!})
                            </a>
                        </li>
                    @else
                        <li @if (\Input::get('order_status') == $os) class="active" @endif>
                            <a href="{!! route_url('', null, ['order_status'=>$os]) !!}">
                                {!! $os_desc !!} ({!! isset($order_status_nums[$os]) ? $order_status_nums[$os] : 0 !!})
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <div class="tab-content">
                {!! Form::model(\Input::all(),['method' => 'get', 'class'=> 'form-inline form-group-sm']) !!}
                {!! Form::hidden('order_status', null) !!}
                {!! Form::select('field', $fields,null, ['class'=> 'form-control',]) !!}
                {!! Form::text('kw', null, ['placeholder' => '搜索值', 'class' => 'w120 form-control', 'id'=>'search_kw']) !!}
                {!! Form::select('cancel_custom', \App\Models\PlatformOrder::kvCancelCustom(), null, ['id'=>'cancel_type', 'placeholder' => '撤销类型(全部)', 'class'=>'form-control']) !!}
                {!! Form::select('kf_status', \App\Models\PlatformOrder::kvKfStatus(), null, ['id'=>'kf_status', 'placeholder' => '客服介入状态', 'class'=>'form-control']) !!}
                {!! Form::label('publish_account_name', '录单人:') !!}
                {!! Form::text('publish_account_name', null, ['placeholder' => '发单人账号', 'class' => 'small form-control']) !!}
                {!! Form::label('input_date', '录单:') !!}
                {!! Form::text('input_start_date', null, ['id'=> 'J_inputStart', 'class'=>'w96 form-control', 'placeholder'=>'开始时间']) !!}
                {!! Form::text('input_end_date', null, ['id'=> 'J_inputEnd', 'class'=>'w96 form-control', 'placeholder'=>'结束时间']) !!}
                {!! Form::label('input_date', '发单:') !!}
                {!! Form::text('publish_start_date', null, ['id'=> 'J_publishStart', 'class'=>'w96 form-control', 'placeholder'=>'开始时间']) !!}
                {!! Form::text('publish_end_date', null, ['id'=> 'J_publishEnd', 'class'=>'w96 form-control', 'placeholder'=>'结束时间']) !!}
                {!! Form::text('actor_tag_note', null, ['placeholder'=>'角色标签','class'=>'form-control', ]) !!}
                {!! Form::text('order_tag_note', null, [ 'placeholder'=>'订单备注','class'=>'form-control', ]) !!}

                {!! Form::select('accept_platform_account_id', \App\Models\PlatformAccount::kvLinear(), null, ['placeholder'=> '合作方','class'=>'form-control', ]) !!}

                {!! Form::select('source_id', \App\Models\GameSource::kvSourceTitle(), null, ['placeholder'=> '订单来源','class'=>'form-control', ]) !!}
                <script>
				require(['jquery', 'jquery.ui', 'bt3', 'bt3.popover-x'], function ($) {
					$(function () {
						$("#J_inputStart").datepicker({
							onClose   : function (selectedDate) {
								$("#J_inputEnd").datepicker("option", "minDate", selectedDate);
							},
							dateFormat: "yy-mm-dd"
						});
						$("#J_inputEnd").datepicker({
							maxDate   : 0,
							onClose   : function (selectedDate) {
								$("#J_inputStart").datepicker("option", "maxDate", selectedDate);
							},
							dateFormat: "yy-mm-dd"
						});
						$("#J_publishStart").datepicker({
							onClose   : function (selectedDate) {
								$("#J_publishEnd").datepicker("option", "minDate", selectedDate);
							},
							dateFormat: "yy-mm-dd"
						});
						$("#J_publishEnd").datepicker({
							maxDate   : 0,
							onClose   : function (selectedDate) {
								$("#J_publishStart").datepicker("option", "maxDate", selectedDate);
							},
							dateFormat: "yy-mm-dd"
						});
					})
				})
                </script>
                <table class="table table-bordered mt5">
                    <tr>
                        <td>
                            <div class="btn btn-danger">
                                {!! Form::label('is_urgency', '加急') !!}
                                {!! Form::checkbox('is_urgency', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            <div class="btn btn-warning">
                                {!! Form::label('is_question', '问题单') !!}
                                {!! Form::checkbox('is_question', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            @if (\Input::get('is_question'))
                                {!! Form::select('question_type', \App\Models\PlatformOrder::kvQuestionType(), null, ['placeholder'=> '问题订单类型','class'=>'form-control input-sm', ]) !!}
                                {!! Form::select('question_status', \App\Models\PlatformOrder::kvQuestionStatus(), null, ['placeholder'=> '问题状态','class'=>'form-control input-sm', ]) !!}
                                {!! Form::select('question_handle_account_id', $handle_account, null, ['placeholder' => '处理人','class'=>'form-control input-sm', ]) !!}
                            @endif
                            <div class="btn btn-default">
                                {!! Form::label('is_employee', '员工单') !!}
                                {!! Form::checkbox('is_employee', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            <div class="btn btn-default">
                                {!! Form::label('is_publish', '发布单') !!}
                                {!! Form::checkbox('is_publish', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            @if (\Input::get('is_employee'))
                                {!! Form::select('employee_id', $employee_list, null, ['placeholder'=> '选择员工','class'=>'form-control', ]) !!}
                            @endif
                            <div class="btn btn-info">
                                {!! Form::label('game_id', '游戏') !!}
                            </div>
                            {!! Form::select('game_id', \App\Models\GameName::kvLinear(), null, ['id'=>'game_id', 'class'=>'form-control']) !!}
                            <i id="server_ctr">
                                {!!Form::select('server_id', ['请选择游戏'], null, ['id' => 'server_id', 'class'=>'form-control'])!!}
                            </i>
                            <i id="type_ctr">
                                {!!Form::select('type_id', ['请选择代练类型'], null, ['id' => 'type_id', 'class'=>'form-control'])!!}
                            </i>
                            <div class="btn btn-info">
                                {!! Form::label('is_input_timeout', '录单未接手') !!}
                                {!! Form::checkbox('is_input_timeout', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            @if (\Input::get('is_input_timeout'))
                                @if ($input_timeouts)
                                    {!! Form::select('input_timeout', $input_timeouts, null, ['placeholder'=> '录单未接手时间','class'=>'form-control', ]) !!}
                                @else
                                    <div class="btn btn-warning">
                                        后台未设置
                                    </div>
                                @endif
                            @endif
                            <div class="btn btn-warning">
                                {!! Form::label('is_ing_timeout', '订单超时') !!}
                                {!! Form::checkbox('is_ing_timeout', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            <div class="btn btn-info">
                                {!! Form::label('is_tgp_question', '战绩问题单') !!}
                                {!! Form::checkbox('is_tgp_question', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>

                            <div class="btn btn-danger">
                                {!! Form::label('is_renew', '续单') !!}
                                {!! Form::checkbox('is_renew', 1, null, ['class'=> 'J_submit', 'data-method'=> 'get', 'data-ajax'=> 'false']) !!}
                            </div>
                            @if (\Input::get('is_ing_timeout'))
                                @if ($ing_timeouts)
                                    {!! Form::select('ing_timeout', $ing_timeouts, null, ['placeholder'=> '订单超时时间','class'=>'form-control', ]) !!}
                                @else
                                    <div class="btn btn-warning">
                                        后台未设置
                                    </div>
                                @endif
                            @endif
                            <div class="pull-right">
                                {!! Form::text('pagesize', $_pagesize, ['class'=> 'form-control w48','placeholder' => '分页数量']) !!}
                                <div class="btn btn-default">
                                    {!! Form::label('export', '导出') !!}
                                    {!! Form::checkbox('export', 1, null) !!}
                                </div>
                                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                            </div>
                        </td>
                    </tr>
                </table>
            {!! Form::close() !!}

            <!-- 数据表格 -->
                <table class="table table-hover mt5 table-condensed">
                    <tr>
                        <th class="w72">订单ID</th>
                        <th class="w96">
                            录入时间
                            {!! Form::order('created_at') !!}
                        </th>
                        <th class="w96">
                            发单时间
                            {!! Form::order('published_at') !!}
                        </th>
                        <th class="w72">接单方</th>
                        <th class="w96">区服</th>
                        <th class="w240">标题</th>
                        <th class="w96">订单价格</th>
                        <th class="w120">标签</th>
                        <th class="w120">角色名</th>
                        <th class="w96">号主联系方式</th>
                        <th class="w84">问题时间 {!! Form::order('question_at') !!}</th>
                        <th class="w120">当前状态</th>
                        <th class="w96">完成时间</th>
                        <th class="w96">
                            剩余时间
                            {!! Form::order('order_left_hours') !!}
                        </th>
                        <th class="w72">
                            战绩
                        </th>
                        <th class="w72">
                            胜率
                            {!! Form::order('tgp') !!}
                        </th>
                        <th class="w84">
                            操作

                        </th>
                    </tr>
                    @foreach($items as $item)
                        <tbody class="hover J_order"
                               data-href="{!! route('dsk_platform_order.detail', ['id' => $item->order_id]) !!}"
                               data-title="
                        @can('show_urgency', $item)
                                       <i class='text-danger'>[急]</i>
                                   @endcan
                               @can('show_renew', $item)
                                       <i class='text-danger'>[续]</i>
                                   @endcan
                               @if ($item->order_status != \App\Models\PlatformOrder::ORDER_STATUS_CREATE)
                               @if ($item->order_lock)
                                       <i class='fa fa-lock text-danger'></i>
                                   @else
                                       <i class='fa fa-unlock text-success'></i>
                                   @endif
                               @endif {{$item->order_title}}"
                               data-width="1000"
                               data-height="750"
                               data-shade_close="true">
                        <tr class="border {!! $item->order_color ? 'show-color-'.$item->order_color: '' !!}">
                            <td>{{$item['order_id']}}</td>
                            <td>{{ \App\Lemon\Repositories\Sour\LmTime::datetime($item->created_at, '2-2')}}</td>
                            <td>{{ $item->published_at ? \App\Lemon\Repositories\Sour\LmTime::datetime($item->published_at, '2-2') : '--'}}</td>
                            <td>
                                {{--接单方进行分配--}}
                                @can('batch_publish', $item)
                                    <a href="{!! route('dsk_platform_order.batch_publish', [$item->order_id]) !!}"
                                       class="J_iframe" data-title="分配订单到平台" data-width="730" data-height="450"
                                       data-shade_close="true">
                                        <i class="fa fa-paper-plane-o fa-lg text-info" data-toggle="tooltip"
                                           title="分配订单"></i>
                                    </a>
                                @endcan
                                {!! \App\Models\PlatformOrder::kvAcceptPlatform($item->accept_platform, $item->employee_publish) !!}
                                {{--接单方进行分配--}}
                                @can('publish_employee', $item)
                                    <a href="{!! route('dsk_platform_employee.publish_employee', [$item->order_id]) !!}"
                                       class="J_iframe" data-title="分配订单到员工" data-width="730" data-height="450"
                                       data-shade_close="true">
                                        <i class="fa fa-paper-plane fa-lg orange" data-toggle="tooltip"
                                           title="分配订单到员工"></i>
                                    </a>
                                @endcan
                            </td>
                            <td>{{$item['game_area']}}</td>
                            <td>
                                <a href="{!! route('dsk_platform_order.detail', [$item->order_id]) !!}"
                                   data-width="1000"
                                   data-height="750"
                                   data-shade_close="true"
                                   class="J_iframe">
                                    @if ($item->is_re_order)
                                        <i class="fa fa-bolt fa-lg text-dark" data-toggle="tooltip"
                                           title="重发的订单"></i>
                                    @endif
                                    @can('show_urgency', $item)
                                        <i class="text-danger">[急]</i>
                                    @endcan
                                    @can('show_renew', $item)
                                        <i class="text-danger">[续]</i>
                                    @endcan
                                    @if ($item->order_status != \App\Models\PlatformOrder::ORDER_STATUS_CREATE)
                                        @if ($item->order_lock)
                                            <i class="fa fa-lock text-danger"
                                               data-toggle="tooltip"
                                               title="{!! \App\Models\PlatformOrder::kvOrderLock($item->order_lock) !!}"></i>
                                        @else
                                            <i class="fa fa-unlock text-success"
                                               data-toggle="tooltip"
                                               title="{!! \App\Models\PlatformOrder::kvOrderLock($item->order_lock) !!}"></i>
                                        @endif
                                    @endif
                                    {{$item->order_title}}
                                </a>
                            </td>
                            <td class="fa text-danger">{{$item['order_price']}}</td>
                            <td>
                                <i class="fa fa-sticky-note-o J_p_note" data-title="角色备注" data-type="actor"
                                   data-id="{!! $item->order_id !!}"
                                   @if ($item->actor_tag_note)data-toggle="tooltip"
                                   title="{!! $item->actor_tag_note !!}" @endif
                                   data-value="{!! $item->actor_tag_note !!}"> {!! $item->actor_tag_note !!}</i>
                            </td>
                            <td>{{$item['game_actor']}}

                                @if ($item['order_qq'])
                                    {!! im($item['order_qq'], 'qq') !!}
                                @endif
                            </td>
                            <td>{!! im($item->order_get_in_mobile, 'mobile') !!}</td>
                            <td>
                                {!! $item->question_at ? $item->question_at->format('m-d H:i') : '--' !!}
                            </td>
                            <td>
                                @if($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                    {!! \App\Models\PlatformOrder::kvCancelType($item->cancel_type) !!}
                                @endif
                                {{ \App\Models\PlatformOrder::kvOrderStatus($item->order_status) }}
                                @if($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                    {!! \App\Models\PlatformOrder::kvCancelStatus($item->cancel_status) !!}
                                @endif
                            </td>
                            <td>
                                {!! $item->ended_at ? \App\Lemon\Repositories\Sour\LmTime::datetime($item->ended_at,'2-2') : '--' !!}
                            </td>
                            <td>
                                @if (!in_array($item->order_status, [\App\Models\PlatformOrder::ORDER_STATUS_OVER]))
                                    {!!  \App\Lemon\Repositories\Sour\LmTime::surplus($item->created_at,$item->order_hours) !!}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{$item->tgp_win}}/ {{$item->tgp_num}}
                            </td>
                            <td>
                                @if($item->tgp_num > 0)
                                    {{round($item->tgp_win/$item->tgp_num*100,2)}}%
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @can('question', $item)
                                    <a href="{!! route('dsk_platform_order.question', [$item->order_id]) !!}"
                                       class="J_iframe" data-title="提交问题" data-width="400"
                                       data-shade_close="true"
                                    >
                                        <i class="fa fa-question-circle fa-lg text-info" data-toggle="tooltip"
                                           title="提交问题"></i>
                                    </a>
                                @endcan
                                @can('handle_question', $item)
                                    <a href="{!! route('dsk_platform_order.handle_question', [$item->order_id]) !!}"
                                       class="J_iframe" data-title="处理问题" data-width="400"
                                       data-shade_close="true"
                                    >
                                        <i class="fa fa-question-circle fa-lg text-danger" data-toggle="tooltip"
                                           title="处理问题"></i>
                                    </a>
                                @endcan
                                @can('edit', $item)
                                    <a href="{!! route('dsk_platform_order.edit', [$item->order_id]) !!}">
                                        <i class="fa fa-edit fa-lg text-info" data-toggle="tooltip" title="编辑"></i>
                                    </a>
                                @endcan
                                @can('destroy', $item)
                                    <a href="{!! route('dsk_platform_order.destroy', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-close fa-lg text-danger" data-toggle="tooltip" title="删除本次发单"
                                           data-confirm="确认删除订单 ? "></i>
                                    </a>
                                @endcan
                                @can('reload', $item)
                                    @if($item->platformAccept && $item->accept_platform != 'employee')
                                        <a href="{!! route('dsk_platform_order.reload', [$item->order_id]) !!}"
                                           class="J_request">
                                            <i class="fa fa-refresh fa-lg text-info" data-toggle="tooltip"
                                               title="同步所有平台订单信息"></i>
                                        </a>
                                    @endif
                                @endcan
                                @can('order_re_publish', $item)
                                    <a href="{!! route('dsk_platform_order.order_re_publish', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-bolt fa-lg text-info" data-toggle="tooltip" title="重新创建订单"></i>
                                    </a>
                                @endcan
                                @can('re_publish', $item)
                                    <a href="{!! route('dsk_platform_order.re_publish', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-repeat fa-lg text-info" data-toggle="tooltip"
                                           title="删除服务器订单并且重发本订单"></i>
                                    </a>
                                @endcan
                                @can('enable_urgency', $item)
                                    <a href="{!! route('dsk_platform_order.enable_urgency', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-flag fa-lg text-warning" data-toggle="tooltip" title="设定紧急"></i>
                                    </a>
                                @endcan
                                @if($item->is_renew == 1)
                                    <a href="{!! route('dsk_platform_order.disable_renew', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-lg fa-share-square-o" data-toggle="tooltip" title="取消续单"></i>
                                    </a>
                                @else
                                    <a href="{!! route('dsk_platform_order.enable_renew', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-lg fa-share-square" data-toggle="tooltip" title="设定续单"></i>
                                    </a>
                                @endif
                                @can('disable_urgency', $item)
                                    <a href="{!! route('dsk_platform_order.disable_urgency', [$item->order_id]) !!}"
                                       class="J_request">
                                        <i class="fa fa-flag-o fa-lg text-success" data-toggle="tooltip"
                                           title="取消紧急"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>

                        <tr>
                            <td colspan="6">
                                @if ($item->platformAccept)
                                    @if ($item->platformAccept->platform == \App\Models\PlatformAccount::PLATFORM_YI)
                                        <i class="fa fa-edge text-success">
                                    @endif
                                            @if ($item->platformAccept->platform == \App\Models\PlatformAccount::PLATFORM_BAOZI)
                                                <i class="fa fa-certificate text-success">
                                                    @endif
                                            @if ($item->platformAccept->platform == \App\Models\PlatformAccount::PLATFORM_YQ)
                                                <i class="fa fa-gamepad text-success">
                                            @endif
                                            @if ($item->platformAccept->platform == \App\Models\PlatformAccount::PLATFORM_MAO)
                                                <i class="fa fa-github-alt text-success">
                                            @endif
                                                    @if ($item->platformAccept->platform == \App\Models\PlatformAccount::PLATFORM_TONG)
                                                        <i class="fa fa-rebel text-success">
                                                    @endif
                                                            @if ($item->platformAccept->platform == \App\Models\PlatformAccount::PLATFORM_MAMA)
                                                                <i class="fa fa-optin-monster text-success">
                                                            @endif
                                                                    @if ($item->platformAccount)
                                                                        {!! $item->platformAccount->note !!}
                                                                    @else
                                                                        @if ($item->platformAccept->platform == \App\Models\PlatformAccount::Employee)
                                                                            <i class="fa fa-user-circle-o text-success">
                                                                                {{ $item->ePam ? $item->ePam->account_name : '-' }}
                                                                            </i>
                                                                        @else
                                                                            [账号已删除]
                                                                        @endif
                                                                    @endif
                                                                </i>
                                                            @else
                                                                @if ($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_PUBLISH)
                                                                    @if (!$item->platformStatus->isEmpty())
                                                                        <table>
                                                                            <tr>
                                                                                @foreach($item->platformStatus as $status)
                                                                                    <td>
                                                                                        @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_YI)
                                                                                            <a href="{!! route('dsk_platform_status_yi.show', [$status->id]) !!}"
                                                                                               class="J_iframe"
                                                                                               data-title="易代练未接单控制台">
                                                                                                <i class="fa fa-edge {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_YI) !!}"
                                                                                                   data-toggle="tooltip"
                                                                                                   title="{!! $status->yi_pt_message !!}">
                                                                                                    {!!  $status->pt_account_note !!}
                                                                                                </i>
                                                                                            </a>
                                                                                        @endif
                                                                                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_BAOZI)
                                                                                                <a href="{!! route('dsk_platform_status_baozi.show', [$status->id]) !!}"
                                                                                                   class="J_iframe"
                                                                                                   data-title="电竞包子未接单控制台">
                                                                                                    <i class="fa fa-certificate {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_BAOZI) !!}"
                                                                                                       data-toggle="tooltip"
                                                                                                       title="{!! $status->baozi_pt_message !!}">
                                                                                                        {!!  $status->pt_account_note !!}
                                                                                                    </i>
                                                                                                </a>
                                                                                            @endif
                                                                                        @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_YQ)
                                                                                            <a href="{!! route('dsk_platform_status_yq.show', [$status->id]) !!}"
                                                                                               class="J_iframe"
                                                                                               data-title="17代练未接单控制台">
                                                                                                <i class="fa fa-gamepad {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_YQ) !!}"
                                                                                                   data-toggle="tooltip"
                                                                                                   title="{!! $status->yq_pt_message !!}">
                                                                                                    {!!  $status->pt_account_note !!}
                                                                                                </i>
                                                                                            </a>
                                                                                        @endif
                                                                                        @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_MAO)
                                                                                            <a href="{!! route('dsk_platform_status_mao.show', [$status->id]) !!}"
                                                                                               class="J_iframe"
                                                                                               data-title="代练猫未接单控制台">
                                                                                                <i class="fa fa-github-alt {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_MAO) !!}"
                                                                                                   data-toggle="tooltip"
                                                                                                   title="{!! $status->mao_pt_message !!}">
                                                                                                    {!!  $status->pt_account_note !!}
                                                                                                </i>
                                                                                            </a>
                                                                                        @endif
                                                                                        @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_TONG)
                                                                                            <a href="{!! route('dsk_platform_status_tong.show', [$status->id]) !!}"
                                                                                               class="J_iframe"
                                                                                               data-title="代练通未接单控制台">
                                                                                                <i class="fa fa-rebel {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_TONG) !!}"
                                                                                                   data-toggle="tooltip"
                                                                                                   title="{!! $status->tong_pt_message !!}">
                                                                                                    {!!  $status->pt_account_note !!}
                                                                                                </i>
                                                                                            </a>
                                                                                        @endif
                                                                                        @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_MAMA)
                                                                                            <a href="{!! route('dsk_platform_status_mama.show', [$status->id]) !!}"
                                                                                               class="J_iframe"
                                                                                               data-title="代练妈妈未接单控制台">
                                                                                                <i class="fa fa-rebel {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_MAMA) !!}"
                                                                                                   data-toggle="tooltip"
                                                                                                   title="{!! $status->mama_pt_message !!}">
                                                                                                    {!!  $status->pt_account_note !!}
                                                                                                </i>
                                                                                            </a>
                                                                                        @endif
                                                                                        @if ($status->platform == \App\Models\PlatformAccount::Employee)
                                                                                            <i class="fa fa-user-circle-o text-info">
                                                                                                {{isset($item->ePam) ? $item->ePam->account_name :''}}
                                                                                            </i>

                                                                                        @endif
                                                                                    </td>
                                                                                @endforeach
                                                                            </tr>
                                                                        </table>
                                                                    @else
                                                                        尚无发单信息
                                                                    @endif
                                                            @endif
                                @endif
                            </td>
                            <td colspan="4" class="text-info">
                                {!! $item->last_log !!}
                            </td>
                            <td colspan="4">
                                <i class="fa fa-sticky-note J_p_note" data-title="订单备注" data-type="order"
                                   data-id="{!! $item->order_id !!}"
                                   @if ($item->order_tag_note)data-toggle="tooltip"
                                   title="{!! $item->order_tag_note !!}" @endif
                                   data-value="{!! $item->order_tag_note !!}"> {!! $item->order_tag_note !!}</i>
                            </td>
                            <td colspan="3">
                                {!! $item->question_at ? $item->question_at->format('m-d H:i') : '' !!}
                            </td>
                        </tr>
                        </tbody>
                    @endforeach
                </table>
                <!-- 分页 -->
                <div class="clearfix mt10">
                    <div class="pagination">
                        {!! $items->render() !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
	require(['jquery', 'lemon/util', 'fadan/util', 'jquery.tagging-js'], function ($, util, helper) {
		$(function () {
			helper.server_html('game_id', 'server_ctr', 'server_id', {
				'id'       : 'server_id',
				'server_id': @if (Input::input('server_id')) {!! Input::input('server_id') !!} @else 0 @endif
			});
			helper.type_html('game_id', 'type_ctr', 'type_id', {
				'id'     : 'server_id',
				'type_id': @if (Input::input('type_id')) {!! Input::input('type_id') !!} @else 0 @endif
			});
		});
	});
    </script>
    <script>
	requirejs(['jquery', 'jquery.layer', 'lemon/util'], function ($, layer, util) {
		$('.J_p_note').on('click', function () {
			var type    = $(this).attr('data-type') ? $(this).attr('data-type') : 'actor';
			var content = $(this).attr('data-content');
			var title   = $(this).attr('data-title') ? $(this).attr('data-title') : '输入备注的内容';
			var id      = $(this).attr('data-id');
			//prompt层
			layer.prompt({
				title   : title,
				formType: 2,
				value   : content
			}, function (text) {
				util.make_request('{!! route('dsk_platform_order.note') !!}', {
					'id'   : id,
					'type' : type,
					'value': text
				}, util.splash);
			});
		});
		$('.J_order').on('dblclick', function () {
			var href        = $(this).attr('data-href');
			var title       = $(this).attr('data-title') ? $(this).attr('data-title') : '';
			var width       = parseInt($(this).attr('data-width')) ? parseInt($(this).attr('data-width')) : '500';
			var height      = parseInt($(this).attr('data-height')) ? parseInt($(this).attr('data-height')) : '500';
			var shade_close = $(this).attr('data-shade_close') == 'true';
			layer.open({
				type      : 2,
				content   : href,
				area      : [width + 'px', height + 'px'],
				title     : title,
				shadeClose: shade_close
			});
		})
	})
    </script>
@endsection