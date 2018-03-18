<div class="sour--header_top">
    <div class="container">
        <div class="row">
            <nav class="navbar" role="navigation">
                <div class="navbar-header">
                    <a href="{!! config('app.url') !!}" class="navbar-brand">
                        {!! slt_image('logo/200x100.png', '为代码服务', [
                            'width'=>75,'style'=> 'margin-top:3px;'
                        ]) !!}
                    </a>
                </div>
                <ul class="nav navbar-nav">
                    <li class="{!! route_current( ['slt:fe.js'], 'active') !!}">
                        <a href="{!! route('slt:fe.js') !!}">前端</a>
                    </li>
                    <li class="{!! route_current( ['slt:nav.index'], 'active') !!}">
                        <a href="{!! route('slt:nav.index') !!}">导航</a>
                    </li>
                    <li class="{!! route_current( ['slt:book.my'], 'active') !!}">
                        <a href="{!! route('slt:book.my') !!}">文库</a>
                    </li>
                    <li class="{!! route_current( ['slt:tool'], 'active') !!}">
                        <a href="{!! route('slt:tool') !!}">工具</a>
                    </li>
                </ul>
                @include('slt::inc.user_status')
            </nav>
        </div>

    </div>
</div>