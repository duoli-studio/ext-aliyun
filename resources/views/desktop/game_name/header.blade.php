<div class="content-heading">
    游戏管理
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-default {{ route_current('dsk_game_name.index', 'btn-info') }}"
               href="{{route('dsk_game_name.index')}}"><span>游戏列表</span></a>
            <a class="btn btn-default {{ route_current('dsk_game_name.create', 'btn-info') }}"
               href="{{route('dsk_game_name.create')}}"><span>添加游戏</span></a>
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item['game_name']}}]</span></a>
            @endif
        </div>
    </div>
</div>