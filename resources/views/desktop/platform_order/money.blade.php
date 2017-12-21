@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    @include('desktop.platform_order.header_detail')
    @if ($items->total())
        <table class="table table-narrow" width="100%">
            <tr >
                <th>ID</th>
                <th class="w84">类型</th>
                <th class="w120">操作时间</th>
                <th>说明
                    @if ($items->hasPages())
                        <div class="pagination clearfix">{!! $progress->render() !!}</div>
                    @endif
                </th>
            </tr>
            @foreach($items as $k => $p)
                <tr>
                    <td>{!! $p->id !!}</td>
                    <td>{!! \App\Models\PlatformMoney::kvType($p->type) !!}</td>
                    <td>{!! $p->created_at !!}</td>
                    <td>{!! $p->note !!}</td>
                    <td>{!! $p->log_content !!}</td>
                </tr>
            @endforeach
        </table>
    @else
        @include('desktop.inc.empty')
    @endif
@endsection