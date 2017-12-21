<div class="content-heading">
    资金统计
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-default {{ route_current('dsk_platform_employee.money')?'btn-info':'' }}"
               href="{{route('dsk_platform_employee.money')}}"><span>资金统计</span></a>
            <a class="btn btn-default {{ route_current('dsk_platform_employee.money_list') ?'btn-info':'' }}"
               href="{{route('dsk_platform_employee.money_list')}}"><span>资金明细</span></a>
        </div>
    </div>
</div>