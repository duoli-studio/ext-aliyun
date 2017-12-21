@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    @if ($accept)
        <div class="alert alert-success">
            当前已经接单 : {!! $accept_account->note !!}
        </div>
    @endif
    @if ($un_accept_has_account)
        {!! Form::open(['url'=> route('dsk_platform_order.batch_publish', [$order->order_id]),'id'=> 'form_handle' ])!!}
        <table class="table table-hover">
            <tr>
                @if (!$accept)
                    <th class="w36 txt-center">
                        {!! Form::checkbox('check_all', 'all', null, ['class'=> 'J_checkAll']) !!}
                    </th>
                @endif
                <th class="w72">ID</th>
                <th>账号备注</th>
                {{--<th class="w72">QQ</th>--}}
                <th class="w120">订单数量(进行中)</th>
                <th class="w216">
                    操作
                    @can('reload', $order)
                        <a href="{!! route('dsk_platform_order.reload', [$order->order_id]) !!}" class="J_request">
                            <i class="fa fa-refresh fa-lg text-info" data-toggle="tooltip" data-placement="bottom"
                               title="同步所有平台订单信息"></i>
                        </a>
                    @endcan
                    @can('batch_publish', $order)
                        <a href="{!! route('dsk_platform_order.batch_re_publish', [$order->order_id]) !!}"
                           class="J_request">
                            <i class="fa fa-superpowers fa-lg text-primary" data-toggle="tooltip" data-placement="bottom"
                               title="重发所有已发出订单"></i>
                        </a>
                    @endcan
                </th>
            </tr>
            @foreach($un_accept_platform_accounts as $item)
                <tr class="J_sel_checkbox hover">
                    @if (!$accept)
                        <td class="w36 txt-center">
                            {{--已经发布的禁用--}}
                            @if (isset($platform_status[$item->id]))
                                @can('assign_publish', $platform_status[$item->id])
                                    {!! Form::checkbox('account_id[]', $item->id, null, ['class' => 'J_checkItem']) !!}
                                @else
                                    {!! Form::checkbox('account_id[]', $item->id, null, ['class' => 'J_checkItem', 'disabled']) !!}
                                @endcan
                            @else
                                {{--未发布, 启用--}}
                                {!! Form::checkbox('account_id[]', $item->id, null, ['class' => 'J_checkItem']) !!}
                            @endif
                        </td>
                    @endif
                    <td>{!! $item->id !!}</td>
                    <td>{!! $item->note !!}</td>
                    {{--<td>{!! $item->qq !!}</td>--}}
                    <td>{!! $item->order_num !!}</td>
                    <td>
                        {{--已经点击发布, 检测发布的状态--}}
                        @if (isset($platform_status[$item->id]))
                            {{--这里的数据来自于 blade(platform_order/index)--}}
							<?php $status = $platform_status[$item->id] ?>
                            {{--平台重新发布--}}
                            @if (!$accept)
                                @can('assign_publish', $status)
                                    <span>
										<a href="{!! route('dsk_platform_order.assign_publish', [$order->order_id, $item->id]) !!}"
                                           class="J_request">
											<i class="fa fa-share text-info"
                                               data-toggle="tooltip"
                                               title="发布到 {!! \App\Models\PlatformAccount::kvPlatform($item->platform) !!} 平台"></i>
										</a>
                                    </span>
                                @endcan
                            @endif
                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_YI)
                                <i class="fa fa-edge {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_YI) !!}"
                                   data-toggle="tooltip"
                                   title="{!! $status->yi_pt_message !!}">
                                    {!!  $status->pt_account_note !!}
                                </i>
                            @endif
                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_MAMA)
                                <i class="fa fa-edge {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_MAMA) !!}"
                                   data-toggle="tooltip"
                                   title="{!! $status->mama_pt_message !!}">
                                    {!!  $status->pt_account_note !!}
                                </i>
                            @endif
                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_MAO)
                                <i class="fa fa-github-alt {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_MAO) !!}"
                                   data-toggle="tooltip"
                                   title="{!! $status->mao_pt_message !!}">
                                    {!!  $status->pt_account_note !!}
                                </i>
                            @endif
                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_TONG)
                                <i class="fa fa-rebel {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_TONG) !!}"
                                   data-toggle="tooltip"
                                   title="{!! $status->tong_pt_message !!}">
                                    {!!  $status->pt_account_note !!}
                                </i>
                            @endif
                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_YQ)
                                <i class="fa fa-edge {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_YQ) !!}"
                                   data-toggle="tooltip"
                                   title="{!! $status->yq_pt_message !!}">
                                    {!!  $status->pt_account_note !!}
                                </i>
                            @endif
                            @if ($status->platform == \App\Models\PlatformAccount::PLATFORM_BAOZI)
                                <i class="fa fa-rebel {!! pt_status_class($status, \App\Models\PlatformAccount::PLATFORM_BAOZI) !!}"
                                   data-toggle="tooltip"
                                   title="{!! $status->baozi_pt_message !!}">
                                    {!!  $status->pt_account_note !!}
                                </i>
                            @endif

                            {{--各个平台的删除--}}
                            @can('mao_delete', $status)
                                <a href="{!! route('dsk_platform_status_mao.delete', [$status]) !!}"
                                   class="J_request text-info" data-toggle="tooltip"
                                   title="删除现有订单">
                                    <i class="fa fa-remove text-danger"> 删除</i>
                                </a>
                            @endcan
                            @can('mama_delete', $status)
                                <a href="{!! route('dsk_platform_status_mama.delete', [$status]) !!}"
                                   class="J_request text-info" data-toggle="tooltip"
                                   title="删除现有订单">
                                    <i class="fa fa-remove text-danger"> 删除</i>
                                </a>
                            @endcan
                            @can('tong_delete', $status)
                                <a href="{!! route('dsk_platform_status_tong.delete', [$status]) !!}"
                                   class="J_request text-info" data-toggle="tooltip"
                                   title="删除现有订单">
                                    <i class="fa fa-remove text-danger"> 删除</i>
                                </a>
                            @endcan
                            @can('yi_delete', $status)
                                <a href="{!! route('dsk_platform_status_yi.delete', [$status]) !!}"
                                   class="J_request text-info" data-toggle="tooltip"
                                   title="删除现有订单">
                                    <i class="fa fa-remove text-danger"> 删除</i>
                                </a>
                            @endcan
                            @can('yq_delete', $status)
                                <a href="{!! route('dsk_platform_status_yq.delete', [$status]) !!}"
                                   class="J_request text-info" data-toggle="tooltip"
                                   title="删除现有订单">
                                    <i class="fa fa-remove text-danger"> 删除</i>
                                </a>
                            @endcan
                            @can('baozi_delete', $status)
                                <a href="{!! route('dsk_platform_status_baozi.delete', [$status]) !!}"
                                   class="J_request text-info" data-toggle="tooltip"
                                   title="删除现有订单">
                                    <i class="fa fa-remove text-danger"> 删除</i>
                                </a>
                            @endcan
                        @else
                            {{--尚未点击发布时候的状态--}}
                            <a href="{!! route('dsk_platform_order.assign_publish', [$order->order_id, $item->id]) !!}"
                               id="form_handle" class="J_request">
                                <i class="fa fa-share text-info"
                                   data-toggle="tooltip"
                                   title="发布到 {!! \App\Models\PlatformAccount::kvPlatform($item->platform) !!} 平台"></i>
                            </a>
                            @if (isset($un_accept_publish_reason[$item->id.'_error']) && $un_accept_publish_reason[$item->id.'_error'])
                                <i class="fa fa-commenting-o text-danger"> {!! $un_accept_publish_reason[$item->id.'_error'] !!}</i>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            @if (!$accept)
                <tr>
                    <td colspan="5">
                        {!! Form::button('批量发布', ['type'=>'submit', 'class'=> 'btn btn-primary btn-sm']) !!}
                    </td>
                </tr>
            @endif
        </table>
        {!! Form::close() !!}
    @else
        <div class="center-block flash-danger">
            <p>无绑定发单账号资料</p>
        </div>
    @endif
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate'], function ($, util) {
            var btn_selector = '#form_handle button:submit';
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    util.button_interaction(btn_selector);
                    $(btn_selector).html('发单中...');
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'bt3_ajax');
            $('#form_handle').validate(conf);

            $('.J_sel_checkbox').on('click', function () {
                $(this).find('input[type=checkbox]').prop('checked', !$(this).find('input[type=checkbox]').prop('checked'));
            })
        });
    </script>
@endsection