<!-- top navbar-->
<header class="topnavbar-wrapper">
    <!-- START Top Navbar-->
    <nav role="navigation" class="navbar topnavbar">
        <!-- START navbar header-->
        <div class="navbar-header">
            <a href="#" class="navbar-brand">
                <div class="brand-logo">
                    <img src="/assets/images/3rd/angle/logo.png" alt="App Logo" class="img-responsive">
                </div>
                <div class="brand-logo-collapsed">
                    <img src="/assets/images/3rd/angle/logo-single.png" alt="App Logo" class="img-responsive">
                </div>
            </a>
        </div>
        <!-- END navbar header-->
        <!-- START Nav wrapper-->
        <div class="nav-wrapper">
            <!-- START Left navbar-->
            <ul class="nav navbar-nav">
                <li title="切换菜单样式" data-toggle="tooltip" data-placement="bottom">
                    <!-- Button used to collapse the left sidebar. Only visible on tablet and desktops-->
                    <a href="#" data-trigger-resize="" data-toggle-state="aside-collapsed" class="hidden-xs">
                        <em class="fa fa-navicon"></em>
                    </a>
                    <!-- Button to show/hide the sidebar on mobile. Visible on mobile only.-->
                    <a href="#" data-toggle-state="aside-toggled" data-no-persist="true"
                       class="visible-xs sidebar-toggle">
                        <em class="fa fa-navicon"></em>
                    </a>
                </li>
                <li>
                    <a href="#" title="用户角色 : {#}" data-toggle="tooltip" data-placement="bottom">
                        <em class="icon-user"> </em>
                       {{--
                        @if ($_desktop->realname)
                            {{$_desktop->realname}}
                        @else
                            {{$_pam->account_name}}
                        @endif
                        --}}
                    </a>
                </li>
                <li>
                    <a href="#" title="修改密码" data-toggle="tooltip"
                       data-placement="bottom">
                        <em class="icon-key"></em>
                    </a>
                </li>
                <li>
                    <a href="#" title="退出登录" data-toggle="tooltip"
                       data-placement="bottom">
                        <em class="icon-logout"></em>
                    </a>
                </li>
            </ul>
            <!-- END Left navbar-->
            <!-- START Right Navbar-->
            <ul class="nav navbar-nav navbar-right">
                <!-- Search icon-->
                <li>
                    <a href="#" data-search-open="">
                        <em class="icon-magnifier"></em>
                    </a>
                </li>
                <li class="visible-lg">
                    <a href="#" data-toggle-fullscreen="">
                        <em class="fa fa-expand"></em>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#">
                        <em class="icon-question" data-toggle="tooltip" data-placement="bottom" title="问题订单"></em>
                        @if($_qst_num)
                            <div class="label label-danger">{!! $_qst_num !!}</div>
                        @endif
                    </a>
                </li>
                <!-- START Offsidebar button-->
                <li>
                    <a href="#" data-toggle-state="offsidebar-open" data-no-persist="true">
                        <em class="icon-notebook"></em>
                    </a>
                </li>
                <!-- END Offsidebar menu-->
            </ul>
            <!-- END Right Navbar-->
        </div>
        <!-- END Nav wrapper-->
    </nav>
    <!-- END Top Navbar-->
</header>