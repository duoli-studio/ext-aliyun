<div class="content-heading">
    游戏服务器管理
    <div class="pull-right">
        <div class="btn-group">
            <a href="{{route('dsk_game_server.index')}}"
               class="btn btn-default {{ ($_route == 'dsk_game_server.index') ? 'btn-info' : '' }}"><span>服务器列表</span></a>
            <a href="{{route('dsk_game_server.create')}}"
               class="btn btn-default {{ ($_route == 'dsk_game_server.create') ? 'btn-info' : '' }}"><span>添加服务器</span></a>
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item->server_title}}]</span></a>
            @endif
        </div>
    </div>
</div>