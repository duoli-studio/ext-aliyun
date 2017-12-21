<div class="content-heading">
    同步账号
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-default {{ ($_route == 'dsk_platform_account.index') ? 'btn-info' : '' }}"
               href="{{route('dsk_platform_account.index')}}"><span>账号管理</span></a>
            <a class="btn btn-default {{ ($_route == 'dsk_platform_account.create') ? 'btn-info' : '' }}"
               href="{{route('dsk_platform_account.create')}}"><span>创建账号</span></a>
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item->note}}]</span></a>
            @endif
        </div>
    </div>
</div>