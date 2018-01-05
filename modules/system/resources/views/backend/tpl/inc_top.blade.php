<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li>
				<span class="text-muted welcome-message">
                    你好, {{$_pam->account_name}}
                </span>
            </li>
            <li>
                <a href="{!! route('be:home.logout') !!}">
                    <i class="fa fa-sign-out"></i> 退出
                </a>
            </li>
            @if (\Session::has('dsk_auth'))
                <li>
                    <a href="#" class="text-warning">
                        <i class="fa fa-sign-out text-warning"></i> 退出授权
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</div>