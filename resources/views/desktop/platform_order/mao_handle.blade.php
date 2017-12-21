@can('mao_pub_cancel_apply', $status)
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_cancel_apply" data-title="撤销">
        <span> 申请撤销 </span>
    </button>
@endcan
@can('mao_pub_cancel_cancel', $status)
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_cancel_cancel" data-title="取消撤销">
        <span> 取消撤销</span>
    </button>
@endcan
@can('mao_sd_cancel_handle', $status)
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_sd_cancel_handle" data-title="同意撤销">
        <span> 同意撤销</span>
    </button>
@endcan
@can('mao_kf', $status)
    @if (!$status->mao_is_kf)
        <button class=" btn btn-primary btn-sm mr3 J_dialog text-danger J_delay" data-element="#detail_kf" data-title="申请客服介入">
            <span> 申请客服介入 </span>
        </button>
    @else
        <button class=" btn btn-primary btn-sm mr3 J_dialog text-danger J_delay" data-element="#detail_unkf" data-title="取消客服介入">
            <span> 取消客服介入 </span>
        </button>
    @endif
@endcan
@can('mao_lock', $status)
    @if (!$status->mao_is_lock)
        <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_lock" data-title="锁定订单">
            <span><i class="fa fa-lock"> 锁定 </i></span>
        </button>
    @else
        <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_unlock" data-title="解锁订单">
            <span><i class="fa fa-unlock"> 解锁 </i></span>
        </button>
    @endif
@endcan
@can('mao_ing', $status)
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_money" data-title="补款">
        <span>补款</span></button>
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_time" data-title="补时">
        <span>补时</span></button>
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_game_pwd" data-title="修改密码">
        <span>修改游戏密码</span>
    </button>
@endcan
@can('mao_progress', $status)
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-title="更新进度">
        <span>更新进度</span>
    </button>
@endcan
@can('mao_over', $status)
    <a class=" btn btn-primary btn-sm mr3 J_request J_delay text-danger" data-confirm="确认完成订单?"
       href="{!! route('dsk_platform_status_mao.over', [$status->id, $order->order_id]) !!}">
        <span> 确认完单</span>
    </a>
@endcan
@can('mao_star', $status)
    <button class=" btn btn-primary btn-sm mr3 J_dialog text-info" data-element="#detail_star" data-title="评价">
        <span>评价</span>
    </button>
@endcan



<div id="detail_lock" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.lock', 'id' => 'form_lock']) !!}
    {!! Form::hidden('lock', \App\Lemon\Dailian\Application\Platform\Mao::LOCK_LOCK) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    <table class="table">
        <tr>
            <td>变更状态</td>
            <td>{!! \App\Lemon\Dailian\Application\Platform\Mao::kvLock(\App\Lemon\Dailian\Application\Platform\Mao::LOCK_LOCK) !!}</td>
        </tr>
        <tr>
            <td class="w108">理由:</td>
            <td>
                {!! Form::textarea('reason', null, ['class' => 'small']) !!}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay">
                    <span>提交</span>
                </button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'form');
            $('#form_lock').validate(conf);
        });
    </script>
</div>
<div id="detail_unlock" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.lock', 'id' => 'form_unlock']) !!}
    {!! Form::hidden('lock', \App\Lemon\Dailian\Application\Platform\Mao::LOCK_UNLOCK) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    <table class="table">
        <tr>
            <td>变更状态</td>
            <td>{!! \App\Lemon\Dailian\Application\Platform\Mao::kvLock(\App\Lemon\Dailian\Application\Platform\Mao::LOCK_UNLOCK) !!}</td>
        </tr>
        <tr>
            <td class="w108">理由:</td>
            <td>
                {!! Form::textarea('reason', null, ['class' => 'small']) !!}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay">
                    <span>提交</span>
                </button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'form');
            $('#form_unlock').validate(conf);
        });
    </script>
</div>
<div id="detail_money" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.special', 'id' => 'form_money']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    {!! Form::hidden('type', \App\Lemon\Dailian\Application\Platform\Mao::MODIFY_MONEY) !!}
    <table class="table">
        <tr>
            <td class="w108">补款金额:</td>
            <td>{!! Form::text('money', null, ['class' => 'small']) !!} 元</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>补款</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {

            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                },
                rules        : {
                    money: {
                        required: true,
                        integer : true
                    }
                }
            }, 'form');
            $('#form_money').validate(conf);
        });
    </script>
</div>
<div id="detail_time" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.special', 'id' => 'form_time']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    {!! Form::hidden('type', \App\Lemon\Dailian\Application\Platform\Mao::MODIFY_TIME) !!}
    <table class="table">
        <tr>
            <td class="w108">补时(小时):</td>
            <td>{!! Form::text('time', null, ['class' => 'small']) !!} 小时</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>补时</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {

            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
                rules        : {
                    time: {
                        required: true,
                        integer : true
                    }
                }
            }, 'form');
            $('#form_time').validate(conf);
        });
    </script>
</div>
<div id="detail_game_pwd" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.special', 'id' => 'form_game_pwd']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    {!! Form::hidden('type', \App\Lemon\Dailian\Application\Platform\Mao::MODIFY_GAME_WORD) !!}
    <table class="table">
        <tr>
            <td class="w108">游戏密码:</td>
            <td>{!! Form::text('game_pwd', null, ['class' => 'small']) !!} </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>修改密码</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {

            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                },
                rules        : {
                    game_pwd: {
                        required: true
                    }
                }
            }, 'form');
            $('#form_game_pwd').validate(conf);
        });
    </script>
</div>
<div id="detail_picture" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.progress', 'id' => 'form_picture', 'files'=> true]) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    <table class="table">
        <tr>
            <td class="w108">图片:</td>
            <td>{!! Form::file('picture', null) !!} </td>
        </tr>
        <tr>
            <td class="w108">说明:</td>
            <td>{!! Form::text('message', null) !!} </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>更新文件</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'form');
            $('#form_picture').validate(conf);
        });
    </script>
</div>
<div id="detail_star" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.star', 'id' => 'form_star']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    <table class="table">
        <tr>
            <td class="w108">评价:</td>
            <td>{!! Form::radios('flag', \App\Lemon\Dailian\Application\Platform\Mao::kvStar(), null, [
			  'desktop' => true
			]) !!} </td>
        </tr>
        <tr>
            <td class="w108">评价内容:</td>
            <td>{!! Form::text('message', null) !!} </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>评价</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'form');
            $('#form_star').validate(conf);
        });
    </script>
</div>
<div id="detail_cancel_apply" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.cancel', 'id'=> 'form_mao_pub_cancel']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    {!! Form::hidden('flag', \App\Lemon\Dailian\Application\Platform\Mao::PUB_CANCEL_APPLY) !!}
    <table class="table">
        <tr>
            <td class="w108">撤销标记:</td>
            <td>申请撤销</td>
        </tr>
        <tr>
            <td class="w108">说明</td>
            <td>
                <p class="mb5">
                    {!! Form::label('order_price', '您已预付的代练费') !!} ¥ {!! $order->order_price !!} 元
                </p>
                <div class="form-group clearfix mb5 ">
                    {!! Form::label('order_safe_money', '对方预付的安全保证金') !!} ¥ {!! $order->order_safe_money !!} 元
                </div>
                <div class="form-group clearfix mb5">
                    {!! Form::label('order_safe_money', '对方预付的效率保证金') !!} ¥ {!! $order->order_speed_money !!} 元
                </div>
            </td>
        </tr>
        <tr>
            <td class="w108">支付代练费:</td>
            <td>{!! Form::text('pub_pay', null, ['class'=> 'w96']) !!} </td>
        </tr>
        <tr>
            <td class="w108">赔偿保证金:</td>
            <td>{!! Form::text('sd_pay', null, ['class'=> 'w96']) !!} </td>
        </tr>
        <tr>
            <td class="w108">说明:</td>
            <td>{!! Form::text('message', null) !!} </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>订单撤销</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                },
                rules        : {
                    sd_pay : {
                        required: true,
                        number  : true,
                        max     : {!! $order->order_safe_money + $order->order_speed_money !!}
                    },
                    pub_pay: {
                        required: true,
                        number  : true,
                        max     : {!! $order->order_price !!}
                    },
                    message: {
                        required: true
                    }
                }
            }, 'bt3');
            $('#form_mao_pub_cancel').validate(conf);
        });
    </script>
</div>
<div id="detail_cancel_cancel" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.cancel_cancel']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    {!! Form::hidden('flag', \App\Lemon\Dailian\Application\Platform\Mao::PUB_CANCEL_CANCEL) !!}
    <table class="table">
        <tr>
            <td class="w108">撤销标记:</td>
            <td>取消撤销</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>订单撤销</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
</div>
<div id="detail_sd_cancel_handle" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.cancel_agree']) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    {!! Form::hidden('flag', \App\Lemon\Dailian\Application\Platform\Mao::PUB_CANCEL_AGREE) !!}
    <table class="table">
        <tr>
            <td class="w108">撤销标记:</td>
            <td>处理撤销</td>
        </tr>
        <tr>
            <td class="w108">说明</td>
            <td>
                <p class="mb5">
                    {!! Form::label('order_price', '您已预付的代练费') !!} ¥ {!! $status->mao_order_price !!} 元
                </p>
                <div class="form-group clearfix mb5 ">
                    {!! Form::label('order_safe_money', '需要支付的代练费') !!} (接口未给出)
                </div>
                <div class="form-group clearfix mb5">
                    {!! Form::label('order_safe_money', '代练员支付的赔偿金') !!} (接口未给出)
                </div>
            </td>
        </tr>
        <tr>
            <td class="w108">支付代练费:</td>
            <td>{!! Form::text('pub_pay', null, ['class'=> 'w96']) !!} </td>
        </tr>
        <tr>
            <td class="w108">赔偿保证金:</td>
            <td>{!! Form::text('sd_pay', null, ['class'=> 'w96']) !!} </td>
        </tr>
        <tr>
            <td class="w108">说明:</td>
            <td>{!! Form::text('message', null) !!} </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay"><span>操作</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
</div>
<div id="detail_kf" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.kf', 'id' => 'form_kf']) !!}
    {!! Form::hidden('kf', \App\Lemon\Dailian\Application\Platform\Mao::KF_KF) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    <table class="table">
        <tr>
            <td>变更状态</td>
            <td>{!! \App\Lemon\Dailian\Application\Platform\Mao::kvKf(\App\Lemon\Dailian\Application\Platform\Mao::KF_KF) !!}</td>
        </tr>
        <tr>
            <td class="w108">理由:</td>
            <td>
                {!! Form::textarea('reason', null, ['class' => 'small']) !!}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay">
                    <span>提交</span>
                </button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'form');
            $('#form_kf').validate(conf);
        });
    </script>
</div>
<div id="detail_unkf" class="hidden">
    {!! Form::open(['route' => 'dsk_platform_status_mao.kf', 'id' => 'form_unkf']) !!}
    {!! Form::hidden('kf', \App\Lemon\Dailian\Application\Platform\Mao::KF_UNKF) !!}
    {!! Form::hidden('status_id', $status->id) !!}
    <table class="table">
        <tr>
            <td>变更状态</td>
            <td>{!! \App\Lemon\Dailian\Application\Platform\Mao::kvKf(\App\Lemon\Dailian\Application\Platform\Mao::KF_UNKF) !!}</td>
        </tr>
        <tr>
            <td class="w108">理由:</td>
            <td>
                {!! Form::textarea('reason', null, ['class' => 'small']) !!}
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <button class=" btn btn-primary btn-sm mr3 text-info J_delay">
                    <span>提交</span>
                </button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var conf = util.validate_conf({
                submitHandler: function (form) {
                    $(form).ajaxSubmit({
                        success: util.splash
                    });
                }
            }, 'form');
            $('#form_unkf').validate(conf);
        });
    </script>
</div>