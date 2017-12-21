<div class="mb5 mt5">
    <a href="{!! route('dsk_platform_order.detail', $order->order_id) !!}"
       class="label label-default {!! route_current('dsk_platform_order.detail', 'label-primary') !!}"><span>订单详情</span></a>
    <a href="{!! route('dsk_platform_order.get_in', $order->order_id) !!}"
       class="label label-default {!! route_current('dsk_platform_order.get_in','label-primary') !!}"><span>接单信息</span></a>
    @if (isset($status))
        @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_MAO)

            <a class="label label-default {{ route_current('dsk_platform_status_mao.show', 'label-primary')}}"
               href="{{route('dsk_platform_status_mao.show', [$status->id])}}"><span>[mao]发单信息</span></a>

            <a class="label label-default {{ route_current('dsk_platform_status_mao.detail', 'label-primary') }}"
               href="{{route('dsk_platform_status_mao.detail',  [$status->id])}}"><span>[mao]订单详情</span></a>

            <a class="label label-default {{ route_current('dsk_platform_status_mao.progress', 'label-primary') }}"
               href="{{route('dsk_platform_status_mao.pic_show',  [$status->id])}}"><span>[mao]进度图</span></a>

            <a class="label label-default {{ route_current('dsk_platform_status_mao.message', 'label-primary') }}"
               href="{{route('dsk_platform_status_mao.message',  [$status->id])}}"><span>[mao]留言</span></a>

        @endif
        @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_MAMA)

            <a class="label label-default {{ route_current('dsk_platform_status_mama.show', 'label-primary') ? 'label-primary' : 'label-default' }}"
               href="{{route('dsk_platform_status_mama.show', [$status->id])}}"><span>[mama]发单信息</span></a>

            <a class="label label-default {{ route_current('dsk_platform_status_mama.detail', 'label-primary') }}"
               href="{{route('dsk_platform_status_mama.detail',  [$status->id])}}"><span>[mama]订单详情</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_mama.progress', 'label-primary') }}"
               href="{{route('dsk_platform_status_mama.pic_show',  [$status->id])}}"><span>[mama]进度图</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_mama.message', 'label-primary') }}"
               href="{{route('dsk_platform_status_mama.message',  [$status->id])}}"><span>[mama]留言</span></a>

        @endif
        @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_TONG)

            <a class="label label-default {{ route_current('dsk_platform_status_tong.show', 'label-primary') }}"
               href="{{route('dsk_platform_status_tong.show', [$status->id])}}"><span>[tong]发单信息</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_tong.detail', 'label-primary') }}"
               href="{{route('dsk_platform_status_tong.detail',  [$status->id])}}"><span>[tong]订单详情</span></a>

            @if (isset($snapshot_url) && $snapshot_url)

                <a class="label label-default {{ route_current($snapshot_url, 'label-primary') }}"
                   href="{!! $snapshot_url !!}" target="_blank"><span>[tong]截图地址</span></a>

            @endif
            @if ($status->tong_is_accept)

                <a class="label label-default {{ route_current('dsk_platform_status_tong.message', 'label-primary') }}"
                   href="{{route('dsk_platform_status_tong.message',  [$status->id])}}"><span>[tong]订单留言</span></a>

            @endif
        @endif
        @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_YI)
            <a class="label label-default {{ route_current('dsk_platform_status_yi.show', 'label-primary') }}"
               href="{{route('dsk_platform_status_yi.show', [$status->id])}}"><span>[yi]发单信息</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_yi.detail', 'label-primary')}}"
               href="{{route('dsk_platform_status_yi.detail',  [$status->id])}}"><span>[yi]订单详情</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_yi.progress', 'label-primary')}}"
               href="{{route('dsk_platform_status_yi.progress',  [$status->id])}}"><span>[yi]进度过程(图)</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_yi.message', 'label-primary') }}"
               href="{{route('dsk_platform_status_yi.message',  [$status->id])}}"><span>[yi]留言内容</span></a>

        @endif
        @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_BAOZI)
            <a class="label label-default {{ route_current('dsk_platform_status_baozi.show', 'label-primary') }}"
               href="{{route('dsk_platform_status_baozi.show', [$status->id])}}"><span>[baozi]发单信息</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_baozi.detail', 'label-primary')}}"
               href="{{route('dsk_platform_status_baozi.detail',  [$status->id])}}"><span>[baozi]订单详情</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_baozi.progress', 'label-primary')}}"
               href="{{route('dsk_platform_status_baozi.progress',  [$status->id])}}"><span>[baozi]进度过程(图)</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_baozi.message', 'label-primary') }}"
               href="{{route('dsk_platform_status_baozi.message',  [$status->id])}}"><span>[baozi]留言内容</span></a>

        @endif
        @if ($order->accept_platform == \App\Models\PlatformAccount::PLATFORM_YQ)
            <a class="label label-default {{ route_current('dsk_platform_status_yq.show', 'label-primary') }}"
               href="{{route('dsk_platform_status_yq.show', [$status->id])}}"><span>[yq]发单信息</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_yq.detail', 'label-primary')}}"
               href="{{route('dsk_platform_status_yq.detail',  [$status->id])}}"><span>[yq]订单详情</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_yq.progress', 'label-primary')}}"
               href="{{route('dsk_platform_status_yq.progress',  [$status->id])}}"><span>[yq]进度过程(图)</span></a>


            <a class="label label-default {{ route_current('dsk_platform_status_yq.message', 'label-primary') }}"
               href="{{route('dsk_platform_status_yq.message',  [$status->id])}}"><span>[yq]留言内容</span></a>

        @endif
    @endif
    @if ($order->accept_platform == \App\Models\PlatformAccount::Employee)

        <a class="label label-default {{ route_current('dsk_platform_status_employee.detail', 'label-primary') }}"
           href="{{route('dsk_platform_status_employee.detail',  [$order->order_id])}}"><span>订单详情</span></a>


        <a class="label label-default {{ route_current('dsk_platform_status_employee.progress', 'label-primary') }}"
           href="{{route('dsk_platform_status_employee.progress',  [$order->order_id])}}"><span>进度过程(图)</span></a>


        <a class="label label-default {{ route_current('dsk_platform_status_employee.message', 'label-primary')}}"
           href="{{route('dsk_platform_status_employee.message',  [$order->order_id])}}"><span>留言内容</span></a>

    @endif

</div>
