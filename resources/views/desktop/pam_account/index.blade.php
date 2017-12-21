@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.pam_account.header')
    <div class="panel panel-default">
        <div class="panel-body">
            <ul class="nav nav-tabs">
                @foreach($_pam_types as $pt)
                    <li class="{{$pt['type']==$account_type ? 'active' : ''}}">
                        <a href="{{route_url('dsk_pam_account.index', null, ['type'=>$pt['type']])}}">{{$pt['name']}}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                {!! Form::model(\Input::all(),['method' => 'get', 'class'=> 'form-inline']) !!}
                {!!Form::hidden('type', $account_type)!!}
                <div class="form-group">
                    {!! Form::label('account_name', '用户名') !!}
                    {!! Form::text('account_name', null, ['placeholder' => '请输入用户名', 'class' => 'form-control']) !!}
                    <span
                            class="sep">&nbsp;</span>
                    {!! Form::label('role_id', '用户角色') !!}
                    {!! Form::select('role_id', $roles, null, ['placeholder'=> '用户角色', 'class' => 'form-control']) !!}
                    <span
                            class="sep">&nbsp;</span>
                    {!! Form::label('pagesize', '分页数') !!}
                    {!! Form::text('pagesize', $_pagesize, ['class' => 'form-control', 'style'=> 'width:60px;']) !!}
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 搜索</button>
                    <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
                </div>
                {!! Form::close() !!}

                @if ($items->total())
                    {!! Form::open() !!}
                    <table class="table">
                        <tr>
                            <th><input type="checkbox" class="J_checkAll"/></th>
                            <th>用户ID</th>
                            <th>用户名</th>
                            <th>登录次数</th>
                            <th>角色名称</th>
                            <th>注册时间</th>
                            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DESKTOP)
                                <th>QQ</th>
                            @endif
                            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_FRONT)
                                <th>QQ</th>
                                <th>联系方式</th>
                                <th>资金</th>
                            @endif
                            <th>操作</th>
                        </tr>
                        @foreach($items as $item)
                            <tr>
                                <td><input type="checkbox" name="account_id[]" value="{{$item->account_id}}" class="J_checkItem"/>
                                </td>
                                <td>{{$item->account_id}}</td>
                                <td>{{$item->account_name}}</td>
                                <td>{{$item->login_times}}</td>
                                <td>{!! pam_roles($item) !!}</td>
                                <td>{{$item->created_at}}</td>
                                @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DESKTOP)
                                    @if (isset($item->desktop))
                                        <td>{{$item->desktop->qq}}</td>
                                    @else
                                        <td> --</td>
                                    @endif
                                @endif
                                @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_FRONT)
                                    @if (isset($item->front))
                                        <th>{{$item->front->qq}}</th>
                                        <th>{{$item->front->mobile}}</th>
                                        <th>{{$item->front->money}}</th>
                                    @else
                                        <td> --</td>
                                        <td> --</td>
                                        <td> --</td>
                                    @endif
                                @endif
                                <td>
                                    @permission('dsk_pam_account.acl')
                                    @if ($item->account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DESKTOP)
                                        <a class="J_iframe" data-toggle="tooltip" title="用户设定"
                                           href="{{route_url('dsk_pam_account.acl',$item->account_id)}}">
                                            <i class="fa fa-cog fa-lg text-info"></i>
                                        </a>
                                    @endif
                                    @endpermission
                                    @if ($item->is_enable == 'Y')
                                        <a class="J_request" data-toggle="tooltip" title="当前启用, 点击禁用"
                                           href="{{route_url('dsk_pam_account.status',null, ['id' => $item->account_id, 'field' => 'is_enable', 'status' => 'N', 'type' => $account_type])}}">
                                            <i class="fa fa-unlock fa-lg text-success"></i>
                                        </a>
                                    @else
                                        <a class="J_request " data-toggle="tooltip" title="当前禁用, 点击启用"
                                           href="{{route_url('dsk_pam_account.status',null, ['id' => $item->account_id, 'field' => 'is_enable', 'status' => 'Y', 'type' => $account_type])}}">
                                            <i class="fa fa-lock fa-lg text-danger"></i>
                                        </a>
                                    @endif
                                    <a data-toggle="tooltip" title="编辑[{{$item->account_name}}]"
                                       href="{{route('dsk_pam_account.edit', [$item->account_id])}}">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                    <a class="J_request" data-toggle="tooltip" title="删除[{{$item->account_name}}]" data-confirm="确认删除？"
                                       href="{{route_url('dsk_pam_account.destroy',null, ['id' =>$item->account_id])}}">
                                        <i class="fa fa-close fa-lg text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <button class="btn btn-danger J_submit J_delay text-danger" data-confirm="批量禁用用户?"
                            data-url="{{route('dsk_pam_account.disable')}}" data-ajax="true"><span>禁用用户</span></button>
                    <button class="btn btn-primary J_submit J_delay text-success" data-confirm="批量启用用户?"
                            data-url="{{route('dsk_pam_account.enable')}}" data-ajax="true"><span>启用用户</span></button>
                    {!! Form::close() !!}
                <!-- 分页 -->
                    <div class="mt10">
                        {!! $items->render() !!}
                    </div>
                @else
                    @include('desktop.inc.empty');
                @endif
            </div>
        </div>
    </div>
@endsection