<ul class="clearfix pull-right header_top-user_status">
	<?php $_pam = \Auth::guard(\System\Models\PamAccount::GUARD_WEB)->user() ?>
    @if ($_pam)
        <li class="dropdown">
            <a href="javascript:@include('slt::inc.collection')">
                <i class="iconfont icon-link"></i> 收藏
            </a>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" title="添加收藏夹">
                <i class="iconfont icon-add"></i>&nbsp;
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{!! route_url('slt:nav.collection') !!}" class="J_iframe">
                        <i class="iconfont icon-shoucangjia"></i> 添加收藏夹
                    </a>
                </li>
                <li>
                    <a href="{!! route('slt:nav.url') !!}" data-width="600" data-height="700" class="J_iframe">
                        <i class="iconfont icon-link"></i> 添加链接
                    </a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown">
                {{--
                {!! Html::image('', '头像', ['width'=> 24, 'height'=>24]) !!}
                --}}
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{!! route('slt:user.profile') !!}"><i class="iconfont icon-setting-user"></i> 账号设置</a>
                </li>
                <li>
                    <a href="{!! route('slt:user.logout') !!}"><i class="iconfont icon-quit"></i> 退出登录</a>
                </li>
            </ul>
        </li>
    @else
        <li>
            <a href="{!! route('slt:user.register') !!}">注册</a>
        </li>
        <li>
            <a href="{!! route('slt:user.login') !!}">登录</a>
        </li>
    @endif
</ul>