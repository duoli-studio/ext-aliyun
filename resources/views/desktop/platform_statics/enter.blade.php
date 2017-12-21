@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        录单员统计
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::model(\Input::all(),['method' => 'get','class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('publish_id', '录入人ID') !!}
                {!! Form::text('publish_id', null, ['placeholder' => '录入人ID', 'class' => 'form-control']) !!}
                @include('desktop.inc.search_range')
                @include('desktop.inc.search_order_status')
                {!! Form::label('pagesize', '分页数') !!}
                {!! Form::text('pagesize', $_pagesize, ['class' => 'form-control w48']) !!}
                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
            </div>
            {!! Form::close() !!}
            @if ($items->total())
                <table class="table table-narrow mt5">
                    <tr >
                        <th class="w84">ID</th>
                        <th class="w84">录入人用户名</th>
                        <th class="w84">发单数量</th>
                        <th class="w84">接单价格</th>
                        <th class="w84">发单价格</th>
                        <th class="w84">转单价格</th>
                        <th class="w84">号主补分加款</th>
                        <th class="w84">代练补分补偿</th>
                        <th class="w84">代练坏单赔偿</th>
                        <th class="w84">补偿号主费用</th>
                        <th class="w84">其他费用</th>
                        <th class="w84">赚取费用</th>
                    </tr>
                    @foreach($items as $k => $item)
                        <tr>
                            <td>{!! $item->publish_id !!}</td>
                            <td>{!! $item->pam ? $item->pam->account_name : '-' !!}</td>
                            <td>{!! $item->publish_count !!}</td>
                            <td>{!! $item->sum_get_in_price !!}</td>
                            <td>{!! $item->sum_order_price !!}</td>
                            <td>{!! $item->sum_zhuandan !!}</td>
                            <td>{!! $item->sum_pub_bufen !!}</td>
                            <td>{!! $item->sum_sd_bufen !!}</td>
                            <td>{!! $item->sum_sd_huaidan !!}</td>
                            <td>{!! $item->sum_pub_buchang !!}</td>
                            <td>{!! $item->sum_other !!}</td>
                            <td>{!! $item->sum_earn !!}</td>
                        </tr>
                    @endforeach
                    @if ($result)
                        <tr class="thead">
                            <td colspan="2">总计:</td>
                            <td>{!! $publish_total !!}</td>
                            <td>{!! $result['sum_get_in_price'] !!}</td>
                            <td>{!! $result['sum_order_price'] !!}</td>
                            <td>{!! $result['sum_zhuandan'] !!}</td>
                            <td>{!! $result['sum_pub_bufen'] !!}</td>
                            <td>{!! $result['sum_sd_bufen'] !!}</td>
                            <td>{!! $result['sum_sd_huaidan'] !!}</td>
                            <td>{!! $result['sum_pub_buchang'] !!}</td>
                            <td>{!! $result['sum_other'] !!}</td>
                            <td>{!! $result['sum_earn'] !!}</td>
                        </tr>
                    @endif
                </table>
                <div class="pull-right">
                    @if ($items->hasPages())
                        <div class="pagination clearfix">{!! $items->render() !!}</div>
                    @endif
                </div>
            @else
                @include('desktop.inc.empty')
            @endif
        </div>
@endsection