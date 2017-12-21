@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.game_name.header')
    <div class="panel panel-default">
        <div class="panel-body">
        @if ($games->total())
            <!-- 数据表格 -->
                <table class="table">
                    <tr>
                        <th class="w72">ID</th>
                        <th>游戏名称</th>
                        <th class="w144">添加时间</th>
                        <th class="w108">操作</th>
                    </tr>
                    @foreach($games as $game)
                        <tr>
                            <td>{{$game['game_id']}}</td>
                            <td>{{$game['game_name']}}</td>
                            <td>{{$game['created_at']}}</td>
                            <td>
                                <a class="fa fa-edit fa-lg"
                                   href="{{route('dsk_game_name.edit', [$game['game_id']])}}"></a>
                                <a class="fa fa-remove fa-lg text-danger J_request"
                                   href="{{route('dsk_game_name.destroy', [$game['game_id']])}}"
                                   data-confirm="您确认要删除此[{!! $game['game_name'] !!}]么?"
                                ></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="mt10">
                    {!! $games->render() !!}
                </div>
            @else
                @include('desktop.inc.empty');
            @endif
        </div>
    </div>
@endsection