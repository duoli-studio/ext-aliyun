<?php
$_pam   = \Auth::guard(\System\Models\PamAccount::GUARD_BACKEND)->user();
$_menus = app('module')->backendMenus()->toArray();
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
						<span class="clear">
							<span class="block m-t-xs">
								<strong class="font-bold">{{$_pam->username}}<b class="caret"></b></strong>
							</span>
						</span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="{!! route('backend:home.logout') !!}">退出</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    SLF
                </div>
            </li>
            @if(isset($_menus))
                @foreach($_menus as $__menu_key => $__module)
                    @foreach($__module as $__sub_menus)
                        <li class="nav-item @if (in_array(\Route::currentRouteName(), $__sub_menus['routes'])) active @endif ">
                            <a href="javascript:;" class="nav-link nav-toggle">
                                {!! $__sub_menus['icon']??'' !!}
                                <span class="title">{{$__sub_menus['title']}}</span>
                            </a>
                            <ul class="nav nav-second-level collapse @if (in_array(\Route::currentRouteName(), $__sub_menus['routes'])) in @endif ">
                                @foreach($__sub_menus['children'] as $nav_key => $nav)
                                    <li class="nav-item @if ($nav['route'] == \Route::currentRouteName()) active @endif">
                                        <a href="{{ $nav['url']}}" class="nav-link">
                                            {!! $nav['icon'] ?? '' !!} {{$nav['title']}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                    @endforeach
                @endforeach
            @endif
        </ul>
    </div>
</nav>