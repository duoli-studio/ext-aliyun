<div class="row border-bottom white-bg sour--header_top">
    <nav class="navbar navbar-static-top" role="navigation">
        <div class="navbar-header">
            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <i class="fa fa-reorder"></i>
            </button>
            <a href="{!! config('app.url') !!}" class="navbar-brand">
                {!! slt_image('logo/200x100.png', '为代码服务', [
                    'width'=>65,'style'=> 'margin-top:3px;'
                ]) !!}
            </a>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
            <ul class="nav navbar-nav">
                <li class="{!! route_current( ['slt:fe.js'], 'active') !!}">
                    <a href="{!! route('slt:fe.js') !!}">前端</a>
                </li>
                <li class="{!! route_current( ['slt:nav.index'], 'active') !!}">
                    <a href="{!! route('slt:nav.index') !!}">导航</a>
                </li>
                {{--
                <li class="{!! route_current( ['web:prd.my_book'], 'active') !!}">
                    <a href="{!! route('web:prd.my_book') !!}">文库</a>
                </li>
                --}}
                <li class="{!! route_current( ['slt:tool'], 'active') !!}">
                    <a href="{!! route('slt:tool') !!}">工具</a>
                </li>
                {{--
                <li class="dropdown">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                    </ul>
                </li>
                --}}

            </ul>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <a href="login.html">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
            </ul>
            @include('slt::inc.user_status')
        </div>
    </nav>
</div>