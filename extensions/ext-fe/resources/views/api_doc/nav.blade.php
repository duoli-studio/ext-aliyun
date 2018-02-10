<nav class="navbar navbar-default">
    <div class="navbar-header">
        <a class="navbar-brand" href="/develop">
            <i class="glyphicon glyphicon-home"></i>
        </a>
    </div>
    <ul class="nav navbar-nav">
        <li><a href="#">[{!! $guard !!}]</a></li>
    </ul>
    <div class="collapse navbar-collapse">
        @if (isset($data['group']) )
            <ul class="nav navbar-nav">
                @foreach($data['group'] as $group_key => $group)
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">{!! $group_key !!} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @foreach($group as $link)
                                <li>
                                    <a href="{!! route_url('',$guard, ['url'=>$link->url, 'method' => $link->type]) !!}">{!! $link->title !!}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        @endif
        @if (isset($self_menu))
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">帮助文档
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @foreach($self_menu as $title => $link)
                            <li><a target="_blank" href="{!! $link !!}">{!! $title !!}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        @endif
    </div>
</nav>