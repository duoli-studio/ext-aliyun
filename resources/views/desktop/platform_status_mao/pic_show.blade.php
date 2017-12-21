@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
<div class="page">
	@include('desktop.platform_order.header_detail')
	<div>
        <table class="table table-narrow" width="100%">
            <tr >
                <th>ID</th>
                <th class="w84">操作时间</th>
                <th class="w72">操作员</th>
                <th>说明</th>
                <th>查看详情</th>
            </tr>
            @foreach($message as $k => $p)
                <tr>
                    <td>{!! $p->order_id !!}</td>
                    <td>{!! \App\Lemon\Repositories\Sour\LmTime::datetime($p->created_at, '2-2') !!}</td>
                    <td>
                        {!! $p->nick_name !!}
                    </td>
                    <td>{!! $p->description !!}</td>
                    <td><a class="fa" href="{!! $p->address !!}" target="_blank">查看详情</a></td>
                </tr>
            @endforeach
        </table>
	</div>
</div>
@endsection