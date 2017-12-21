@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        同步日志
    </div>
    <div class="panel panel-default">
        <div class="panel-body text-sm">
            {!! Form::model(\Input::all(),['method' => 'get','class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('accept_platform', '同步平台') !!}
                {!! Form::select('accept_platform', \App\Models\PlatformAccount::kvPlatform(), null,['placeholder' => '同步平台', 'class' => 'form-control']) !!}
                &nbsp;
                {!! Form::label('sync_status', '信息状态') !!}
                {!! Form::select('sync_status', [
                    'error' => '错误信息',
                    'success' => '成功信息'
                ], null,['placeholder' => '信息状态', 'class' => 'form-control']) !!}
                {!! Form::label('order_id', '订单ID') !!}
                {!! Form::text('order_id', null,['placeholder' => '订单ID', 'class' => 'form-control']) !!}
                {!! Form::text('page', \Input::input('page') ?: 1,['placeholder' => '页码', 'class' => 'form-control']) !!}
                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
            </div>
            {!! Form::close() !!}
            <table class="table mt5">
                <tr >
                    <th class="w72">ID</th>
                    <th class="w108">平台</th>
                    <th class="w108">订单ID</th>
                    <th class="w120">同步状态</th>
                    <th>同步说明</th>
                    <th class="w144">同步时间</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{\App\Models\PlatformAccount::kvPlatform($item->accept_platform)}}</td>
                        <td>{{$item->order_id}}</td>
                        <td>
                            @if ($item->sync_status == 'error')
                                <i class="fa fa-close fa-lg text-danger"></i>
                            @else
                                <i class="fa fa-check fa-lg text-success"></i>
                            @endif
                        </td>
                        <td>{{$item->sync_note}}</td>
                        <td>{{$item->created_at}}</td>
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