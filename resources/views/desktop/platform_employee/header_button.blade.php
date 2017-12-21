<div>
    @can('employee_show', $order)
        @can('employee_progress', $order)
            <a class="btn btn-primary btn-sm J_iframe text-info" data-title="更新进度" data-height="300px"
               href="{{ route('dsk_platform_employee.update_progress', [$order_id]) }}">
                <span>更新进度</span>
            </a>
            <a class="btn btn-primary btn-sm J_iframe text-info" data-title="分配到电脑" data-height="300px"
               href="{{ route('dsk_platform_employee.assign_pc', [$order_id]) }}">
                <span>分配到电脑</span>
            </a>
        @endcan
        @can('employee_exception', $order)
            <a class="btn btn-primary btn-sm J_iframe text-info" data-title="提交异常" data-height="300px"
               href="{{ route('dsk_platform_employee.exception', [$order_id]) }}">
                <span>提交异常</span>
            </a>
        @endcan
        @can('employee_cancel_exception', $order)
            <a class="btn btn-primary btn-sm text-info" data-height="300px"
               href="{{ route('dsk_platform_employee.cancel_exception', [$order_id]) }}">
                <span>取消异常</span>
            </a>
        @endcan
        @can('employee_handle_cancel', $order)
            <a class="btn btn-primary btn-sm J_request text-info" data-confirm="确定要同意撤单?" data-height="300px"
               href="{{ route('dsk_platform_employee.handel_cancel', [$order_id]) }}">
                <span>同意撤销</span>
            </a>
        @endcan
    @endcan

    {{--{!! Form::button('<span>撤销</span>', ['class'=> 'btn btn-small', 'disabled','id'=> 'btn_quash']) !!}--}}
    {{--{!! Form::button('<span>更新进度</span>', ['class'=> 'btn btn-small', 'id'=> 'btn_progress']) !!}--}}
    {{--{!! Form::button('<span>确认完单</span>', ['class'=> 'btn btn-small btn-disabled','disabled', 'id'=> 'btn_over']) !!}--}}
</div>