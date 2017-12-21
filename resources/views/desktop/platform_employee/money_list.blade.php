@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_employee.money_header')
    <div class="panel panel-default">
        <div class="panel-body">
            <!-- 数据表格 -->
            <table class="table">
                <tr>
                    <th class="w72">订单ID</th>
                    <th class="w120">订单价格</th>
                    <th class="w120">订单状态</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item->order_id}}</td>
                        <td>{{$item->order_price}}</td>
                        <td>
                            @if($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                {!! \App\Models\PlatformOrder::kvCancelType($item->cancel_type) !!}
                            @endif
                            {{ \App\Models\PlatformOrder::kvOrderStatus($item->order_status) }}
                            @if($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                {!! \App\Models\PlatformOrder::kvCancelStatus($item->cancel_status) !!}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <!-- 分页 -->
            <div class="clearfix mt10">
                {!! $items->render() !!}
            </div>
        </div>
    </div>
@endsection