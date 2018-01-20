<nav class="navbar navbar-default">
    <div class="navbar-header">
        <a class="navbar-brand" href="{!! route('system:develop.cp.cp') !!}">
            开发平台
        </a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            @foreach($menus as $key => $menu)
                <li>
                    <a href="{!! route('system:develop.cp.cp') !!}#{!! $key !!}">
                        {!! app('poppy')->get($key."::name") !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>