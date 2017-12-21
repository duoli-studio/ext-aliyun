<div class="content-heading">
    角色管理
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-default {{ ($_route == 'dsk_pam_role.index') ? 'btn-info' : '' }}"
               href="{{route_url('dsk_pam_role.index',null, ['type'=> \Request::input('type'),])}}" ><span>角色列表</span></a>
            @permission('dsk_pam_role.create')
            <a class="btn btn-default {{ ($_route == 'dsk_pam_role.create') ? 'btn-info' : '' }}"
               href="{{route_url('dsk_pam_role.create',null, ['type'=> \Request::input('type')])}}"><span>添加角色</span></a>
            @endpermission
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item['role_name']}}]</span></a>
            @endif
        </div>
    </div>
</div>