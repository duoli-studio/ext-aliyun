@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_order.header')
    <div class="panel panel-default">
        <div class="panel-body text-sm">
        {!! Form::model(\Input::all(),['method' => 'get', 'class'=> 'form-inline form-group-sm']) !!}
        {!! Form::select('is_over', \App\Models\BaseConfig::kvYn(), null, ['placeholder' => '完成状态', 'class'=>'form-control']) !!}
        {!! Form::select('is_pay', \App\Models\BaseConfig::kvYn(), null, ['placeholder' => '支付状态', 'class'=>'form-control']) !!}
        {!! Form::button('搜索', ['class'=> 'form-control', 'type'=> 'submit']) !!}
        {!! Form::close() !!}

        <!-- 数据表格 -->
            <table class="table table-hover mt5 table-condensed">
                <tr>
                    <th class="w72">ID {!! Form::order('id') !!}</th>
                    <th class="w72">订单ID</th>
                    <th class="w96">数量</th>
                    <th class="w96">
                        主订单
                    </th>
                    <th class="w72">
                        附订单
                    </th>
                    <th class="w96">
                        是否完成{!! Form::order('is_over') !!}
                    </th>
                    <th class="w96">
                        是否支付{!! Form::order('is_pay') !!}
                    </th>
                    <th>检测时间</th>
                    <th>操作</th>
                </tr>
                @foreach($items as $item)
                    <tr class="border @if ($item->is_pay) show-color-red @endif">
                        <td>{{$item->id}}</td>
                        <td>{{$item->order_id}}</td>
                        <td>{{$item->num}}</td>
                        <td>{!!  \App\Models\PlatformRepeat::descStatus($item->main_status)  !!}</td>
                        <td>{!!  \App\Models\PlatformRepeat::descStatus($item->status) !!}</td>
                        <td>{{ \App\Models\BaseConfig::kvYn($item->is_over)}}</td>
                        <td>{{ \App\Models\BaseConfig::kvYn($item->is_pay)}}</td>
                        <td>{{ $item->created_at }}</td>
                    </tr>
                    @if ($item->content)
                        <tr>
                            <td colspan="8">
                                {!! \App\Models\PlatformRepeat::descContent($item->content) !!}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
            <!-- 分页 -->
            <div class="clearfix mt10">
                <div class="pagination">
                    {!! $items->render() !!}
                </div>
            </div>
        </div>
    </div>
@endsection