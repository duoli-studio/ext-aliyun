<div class="content-heading">
    游戏类型管理
    <div class="pull-right">
        <div class="btn-group">
            <a href="{{route('dsk_game_type.index')}}"
               class="btn btn-default {{ ($_route == 'dsk_game_type.index') ? 'btn-info' : '' }}"><span>类型列表</span></a>
            <a href="{{route('dsk_game_type.create')}}"
               class="btn btn-default {{ ($_route == 'dsk_game_type.create') ? 'btn-info' : '' }}"><span>添加类型</span></a>
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item['type_title']}}]</span></a>
            @endif
        </div>
    </div>
</div>