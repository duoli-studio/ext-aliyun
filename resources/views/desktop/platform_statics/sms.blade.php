@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        短息统计
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            @if ($sms_lists->total())
                <table class="table table-narrow" width="100%">
                    <tr >
                        <th class="w84">订单ID</th>
                        <th class="w84">短信类型</th>
                        <th class="w84">发送时间</th>
                    </tr>
                    @foreach($sms_lists as $k => $item)
                        <tr>
                            <td>{!! $item->order_id !!}</td>
                            <td>{!! \App\Models\PlatformLog::kvSmsType($item->type) !!}</td>
                            <td>{!! $item->created_at !!}</td>
                        </tr>
                    @endforeach
                    @if ($sms_total)
                        <tr class="thead">
                            <td colspan="2">总计:</td>
                            <td>{!! $sms_total !!}条</td>
                        </tr>
                    @endif
                </table>
                <div class="pull-right">
                    @if ($sms_lists->hasPages())
                        <div class="pagination clearfix">{!! $sms_lists->render() !!}</div>
                    @endif
                </div>
            @else
                @include('desktop.inc.empty')
            @endif
        </div>
@endsection