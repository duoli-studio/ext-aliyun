<div class="content-heading">
    游戏来源管理
    <div class="pull-right">
        <div class="btn-group">
            <a href="{{route('dsk_game_source.index')}}"
               class="btn btn-default {{ ($_route == 'dsk_game_source.index') ? 'btn-info' : '' }}"><span>游戏来源</span></a>
            <a href="{{route('dsk_game_source.create')}}"
               class="btn btn-default {{ ($_route == 'dsk_game_source.create') ? 'btn-info' : '' }}"><span>添加游戏来源</span></a>
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item['source_name']}}]</span></a>
            @endif
        </div>
    </div>
</div>