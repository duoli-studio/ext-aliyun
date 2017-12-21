@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_order.header')
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::model(\Input::all(),['method' => 'get','class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::label('account_name', '账户名称') !!}
                {!! Form::text('account_name', null,['placeholder' => '账户名称', 'class' => 'form-control']) !!}
                <span class="sep">&nbsp;</span>
                {!! Form::text('page', \Input::input('page') ?: 1,['placeholder' => '页码',  'class' => 'form-control']) !!}
                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
            </div>
            {!! Form::close() !!}
            <table class="table">
                <tr>
                    <th class="w72">ID</th>
                    <th class="w108">用户ID</th>
                    <th class="w108">用户名</th>
                    <th>文件存储位置</th>
                    <th class="w108">下载</th>
                    <th class="w120">导出时间</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->account_id}}</td>
                        <td>{{$item->pam ? $item->pam->account_name : '-'}}</td>
                        <td>{{$item->storage_path}}</td>
                        <td><a href="{{route_url('',$item->id)}}"><i class="fa fa-download"></i></a></td>
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