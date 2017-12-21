<div class="content-heading">
    订单管理
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-default {{ ($_route == 'dsk_platform_order.index') ? 'btn-info' : '' }}"
               href="{{route('dsk_platform_order.index')}}"><span>订单管理</span></a>
            <a class="btn btn-default {{ ($_route == 'dsk_platform_order.create') ? 'btn-info' : '' }}"
               href="{{route('dsk_platform_order.create')}}"><span>创建订单</span></a>
            @permission('dsk_platform_order.export_index')
            <a class="btn btn-default {{ route_current('dsk_platform_order.export_index')}}"
               href="{{route('dsk_platform_order.export_index')}}"><span>导出记录</span></a>
            @endpermission
            @if (isset($item))
                <a class="btn btn-default btn-info" href="javascript:void(0)"><span>编辑 [{{$item['order_title']}}]</span></a>
            @endif
        </div>
    </div>
</div>