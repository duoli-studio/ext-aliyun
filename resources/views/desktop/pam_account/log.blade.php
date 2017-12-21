@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        账户日志
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <!-- 数据表格 -->
            <table class="table">
                <tr>
                    <th class="w72">ID</th>
                    <th class="w108">用户名</th>
                    <th class="w144">操作时间</th>
                    <th class="w108">成功/失败</th>
                    <th>说明</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item['log_id']}}</td>
                        <td>{{$item['account_name']}}</td>
                        <td>{{$item['created_at']}}</td>
                        <td>{{$item['log_type']}}</td>
                        <td>{{$item['log_content']}}</td>
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