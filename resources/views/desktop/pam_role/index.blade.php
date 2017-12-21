@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.pam_role.header')
    <div class="panel panel-default">
        <div class="panel-body">
            <ul class="nav nav-tabs">
                @foreach($_pam_types as $pt)
                    <li class="{{ $pt['type'] == $type ? 'active' : ''}}">
                        <a href="{{route_url('dsk_pam_account.index', null, ['type'=>$pt['type']])}}">{{$pt['name']}}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                <!-- 数据表格 -->
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <th>角色</th>
                        <th>角色显示名称</th>
                        <th>添加时间</th>
                        <th>编辑时间</th>
                        <th>操作</th>
                    </tr>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{$role->id}}</td>
                            <td>{{$role->role_name}}</td>
                            <td>{{$role->role_title}}</td>
                            <td>{{$role->created_at}}</td>
                            <td>{{$role->updated_at}}</td>
                            <td>
                                @can('menu', $role)
                                    <a class="fa fa-check-square-o fa-lg J_iframe"
                                       data-title="编辑 [{{$role->role_title}}] 权限"
                                       data-width="600"
                                       data-shade_close="true"
                                       href="{{route('dsk_pam_role.menu', [$role->id])}}"></a>
                                @endcan
                                @can('edit', $role)
                                    <a class="fa fa-edit fa-lg" href="{{route('dsk_pam_role.edit', [$role->id])}}"></a>
                                @endcan
                                @can('destroy', $role)
                                    <a class="fa fa-close fa-lg text-danger J_request" data-confirm="确认删除?"
                                       href="{{route('dsk_pam_role.destroy', [$role->id])}}"></a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </table>
                <!-- 分页 -->
                <div class="clearfix mt10">
                    {!! $roles->render() !!}
                </div>
            </div>
        </div>
    </div>
@endsection