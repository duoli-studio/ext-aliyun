<div class="content-heading">
    用户管理
    <div class="pull-right">
        <div class="btn-group">
            <a href="{{route_url('dsk_pam_account.index',null, ['type'=> $account_type])}}"
               class="btn btn-default {{ route_current('dsk_pam_account.index', 'btn-info')}}">用户管理</a>
            <a href="{{route_url('dsk_pam_account.create',null, ['type'=> $account_type])}}"
               class="btn btn-default {{ route_current('dsk_pam_account.create', 'btn-info')}}">添加用户</a>
            @if (isset($item))
                <a class="btn btn-info" href="javascript:void(0)"><span>编辑 [{{$item['account_name']}}]</span></a>
            @endif
        </div>
    </div>
</div>