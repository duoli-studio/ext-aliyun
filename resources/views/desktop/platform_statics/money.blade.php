@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        订单资金统计
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::model(\Input::all(),['method' => 'get','class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('order_id', '订单ID') !!}
                {!! Form::text('order_id', null, ['placeholder' => '订单ID', 'class' => 'form-control']) !!}
                @include('desktop.inc.search_range')
                @include('desktop.inc.search_order_status')
                {!! Form::label('pagesize', '分页数') !!}
                {!! Form::text('pagesize', $_pagesize, ['class'=> 'form-control w48']) !!}
                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
            </div>
            {!! Form::close() !!}
            @if ($items->total())
                <table class="table table-narrow mt5" width="100%">
                    <tr >
                        <th class="w84">ID</th>
                        <th class="w84">接单价格</th>
                        <th class="w84">发单价格</th>
                        <th class="w84">转单价格</th>
                        <th class="w84">号主补分加款</th>
                        <th class="w84">代练补分补偿</th>
                        <th class="w84">代练坏单赔偿</th>
                        <th class="w84">补偿号主费用</th>
                        <th class="w84">其他费用</th>
                        <th class="w84">赚取费用</th>
                        <th class="w120">创建时间</th>
                    </tr>
                    @foreach($items as $k => $p)
                        <tr>
                            <td>{!! $p->order_id !!}</td>
                            <td>{!! $p->order_get_in_price !!}</td>
                            <td>{!! $p->order_price !!}</td>
                            <td>{!! $p->fee_zhuandan !!}</td>
                            <td>{!! $p->fee_pub_bufen !!}</td>
                            <td>{!! $p->fee_sd_bufen !!}</td>
                            <td>{!! $p->fee_sd_huaidan !!}</td>
                            <td>{!! $p->fee_pub_buchang !!}</td>
                            <td>{!! $p->fee_other !!}</td>
                            <td>{!! $p->fee_earn !!}</td>
                            <td>{!! $p->created_at !!}</td>
                        </tr>
                    @endforeach
                    <tr class="thead">
                        <td>总计:</td>
                        <td>{!! $result['order_get_in_price'] !!}</td>
                        <td>{!! $result['order_price'] !!}</td>
                        <td>{!! $result['fee_zhuandan'] !!}</td>
                        <td>{!! $result['fee_pub_bufen'] !!}</td>
                        <td>{!! $result['fee_sd_bufen'] !!}</td>
                        <td>{!! $result['fee_sd_huaidan'] !!}</td>
                        <td>{!! $result['fee_pub_buchang'] !!}</td>
                        <td>{!! $result['fee_other'] !!}</td>
                        <td>{!! $result['fee_earn'] !!}</td>
                        <td>&nbsp;</td>
                    </tr>
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