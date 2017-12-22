<header class="sour--header_top">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse"
                        class="navbar-toggle collapsed" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{!! config('app.url') !!}" class="navbar-brand">
                    {!! Html::image('project/sour/images/logo/200x100.png', '为代码服务', [
                        'width'=>65,'style'=> 'margin-top:3px;'
                    ]) !!}
                </a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    {{--
                    <li class="{!! route_current( ['web:fe.js'], 'active') !!}">
                        <a href="{!! route('web:fe.js') !!}">前端</a>
                    </li>
                    <li class="{!! route_current( ['web:nav.index'], 'active') !!}">
                        <a href="{!! route('web:nav.index') !!}">导航</a>
                    </li>
                    <li class="{!! route_current( ['web:prd.my_book'], 'active') !!}">
                        <a href="{!! route('web:prd.my_book') !!}">文库</a>
                    </li>
                    --}}
                    <li class="{!! route_current( ['slt:tool'], 'active') !!}">
                        <a href="{!! route('slt:tool') !!}">工具</a>
                    </li>
                </ul>
                {{--@include('slt::inc.user_status')--}}
            </div>

        </div>
    </nav>
</header>