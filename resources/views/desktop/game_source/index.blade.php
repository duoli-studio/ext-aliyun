@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.game_source.header')
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::open(['route' => 'dsk_game_source.sort', 'id' => 'form_sort','class'=> 'form-inline']) !!}
            <table width="98%" border="0" cellpadding="5" cellspacing="1" class="table J_hover">
                <tr class="thead thead-space thead-center">
                    <th class="w72">排序</th>
                    <th class="w72">ID</th>
                    <th>游戏来源</th>
                    <th class="w216">管理操作</th>
                </tr>
                @foreach($pageInfo as $item)
                    <tr class='tr'>
                        <td class='txt-center'><input type='text' value='{{$item['list_order']}}' class='w36'
                                                      name='sort[{{$item['source_id']}}]'></td>
                        <td class='txt-center'>{{$item['source_id']}}</td>
                        <td>{{$item['source_name']}}</td>
                        <td class='txt-center'>
                            <a href="{{route('dsk_game_source.edit', ['source_id' => $item['source_id']])}}"><i
                                        class='fa fa-edit fa-lg'></i></a>
                            <a class="J_request"
                               href="{{route('dsk_game_source.destroy', ['source_id' => $item['source_id']])}}"
                               data-confirm="确定删除该游戏来源吗?"><i class='fa fa-close fa-lg text-danger'></i></a>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" valign="middle">
                        <button class="btn btn-primary" type="submit"><span>排序</span></button>
                    </td>
                </tr>
            </table>
            <!-- 分页 -->
            <div class="clearfix mt10">
                {!! $pageInfo->render() !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection