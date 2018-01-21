<nav class="navbar navbar-default">
    <div class="navbar-header">
        <a class="navbar-brand" href="{!! route('system:develop.cp.cp') !!}">
            开发平台
        </a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav metismenu" id="menu">
            @foreach($_menus as $key => $menu)
                <li>
                    <a href="{!! route('system:develop.cp.cp') !!}#{!! $key !!}">
                        {!! app('poppy')->get($key."::name") !!}
                    </a>
                    {{--
                    @foreach($menu as $nav_group)
                        <ul>
                            <li>
                                <a href="#">{!! $nav_group['title'] !!}</a>
                            </li>
                            @if (isset($nav_group['children']) && is_array($nav_group['children']))
                                <ul>
                                    @foreach($nav_group['children'] as $sub)
                                        <li>
                                            <a href="{{ $sub['url'] }}">{{$sub['title']}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </ul>
                    @endforeach
                    --}}
                </li>
            @endforeach
        </ul>
    </div>
    <script>
    require(['jquery', 'jquery.metis-menu'], function($){
	    $("#menu").metisMenu();
    })
    </script>
</nav>