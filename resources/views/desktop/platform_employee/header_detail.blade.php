<div class="mb5 mt5">

    <a class="label label-default {{ route_current('dsk_platform_employee.employee_order_detail', 'label-primary')}}"
       href="{{route('dsk_platform_employee.employee_order_detail', [$order_id])}}"><span>订单详情</span></a>

    @can('employee_show', $order)

        <a class="label label-default {{ route_current('dsk_platform_employee.pic_show', 'label-primary')}}"
           href="{{route('dsk_platform_employee.pic_show', [$order_id])}}"><span>进度图</span></a>


        <a class="label label-default {{ route_current('dsk_platform_employee.message', 'label-primary')}}"
           href="{{route('dsk_platform_employee.message', [$order_id])}}"><span>留言</span></a>
        @if($order->enclosure)
            <a href="{!! $order->enclosure !!}"
               class="label label-primary">下载附件</a>
        @endif
    @endcan

</div>