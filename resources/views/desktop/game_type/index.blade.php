@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.game_type.header')
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::open(['route' => 'dsk_game_type.sort', 'id' => 'form_sort','class'=> 'form-inline']) !!}
            <table width="98%" border="0" cellpadding="5" cellspacing="1" class="table J_hover">
                <tr class="thead thead-space thead-center">
                    <th class="w72">排序</th>
                    <th class="w72">ID</th>
                    <th>类型名称</th>
                    <th>游戏名称</th>
                    <th>创建时间</th>
                    <th class="w216">管理操作</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td class='txt-center'><input type='text' value='{{$item['list_order']}}' class='w36'
                                                      name='sort[{{$item['type_id']}}]'></td>
                        <td>{{$item['type_id']}}</td>
                        <td>{{$item['type_title']}}</td>
                        <td class='txt-center'>{{$item['game']['game_name']}}</td>
                        <td class='txt-center'>{{$item['created_at']}}</td>
                        <td class='txt-center'>
                            <a class="fa fa-edit fa-lg" href="{{route('dsk_game_type.edit', [$item['type_id']])}}"></a>
                            <a class="fa fa-remove fa-lg text-danger J_request"
                               href="{{route('dsk_game_type.destroy', [$item['type_id']])}}"
                               data-confirm="您确认要删除此[{!! $item['type_title'] !!}]么?"
                            ></a>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" valign="middle">
                        <button class="btn btn-primary" type="submit"><span>排序</span></button>
                    </td>
                </tr>
            </table>
            {!! Form::close() !!}
        </div>
    </div>
@endsection