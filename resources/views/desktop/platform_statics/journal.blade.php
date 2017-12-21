@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        订单资金流水
    </div>
    <div class="panel panel-default">
        <div class="panel-body text-sm">
            {!! Form::model(\Input::all(),['method' => 'get','class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('order_id', '订单ID') !!}
                {!! Form::text('order_id', null, ['placeholder' => '订单ID', 'class' => 'form-control']) !!}
                {!! Form::select('type', \App\Models\PlatformMoney::kvType(), null, ['placeholder' => '资金变动类型', 'class' => 'form-control']) !!}
                @include('desktop.inc.search_range')
                {!! Form::label('pagesize', '分页数') !!}
                {!! Form::text('pagesize', $_pagesize, ['class'=> 'form-control w48']) !!}
                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
            </div>
            {!! Form::close() !!}
            @if ($items->total())
                <table class="table table-narrow mt5">
                    <tr >
                        <th class="w84">ID</th>
                        <th class="w84">订单ID</th>
                        <th class="w120">类型</th>
                        <th class="w120">变动金额</th>
                        <th>说明</th>
                        <th class="w144">操作时间</th>
                    </tr>
                    @foreach($items as $k => $p)
                        <tr>
                            <td>{!! $p->id !!}</td>
                            <td>{!! $p->order_id !!}</td>
                            <td>{!! \App\Models\PlatformMoney::kvType($p->type) !!}</td>
                            <td>{!! $p->amount !!}</td>
                            <td>{!! $p->note !!}</td>
                            <td>{!! $p->created_at !!}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3"></td>
                        <td>{!! $amount !!}</td>
                        <td colspan="3"></td>
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