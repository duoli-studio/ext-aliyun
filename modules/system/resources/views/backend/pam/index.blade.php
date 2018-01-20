@extends('system::backend.tpl.default')

@section('backend-breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2>用户管理</h2>
            <ol class="breadcrumb">
                <li> 管理项目用户</li>
            </ol>
        </div>
        <div class="col-lg-4">
            <div class="title-action pull-right">
                <div class="btn-group" role="group">
                    @can('create', \System\Models\PamRole::class)
                        <a href="{{route_url('backend:pam.establish', [], [
                            'type' => $type,
                        ])}}" class="btn btn-default J_iframe">创建用户</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@section('backend-main')
    <ul class="nav nav-tabs">
		<?php $i = 0 ?>
        @foreach($types as $type_key => $type_title)
            <li class="{!! $type == $type_key ? 'active' : '' !!}">
                <a href="{!! route_url('', [], ['type'=> $type_key]) !!}">{!! $type_title !!}</a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        {!! Form::model(\Input::all(),['method' => 'get', 'class'=> 'form-inline mt8']) !!}
        {!!Form::hidden('type', $type)!!}
        <div class="form-group">
            {!! Form::text('username', null, ['placeholder' => '用户名', 'class' => 'form-control']) !!}
            {!! Form::select('role_id', $roles, null, ['placeholder'=> '用户角色', 'class' => 'form-control']) !!}

            {!! Form::label('pagesize', '分页数') !!}
            {!! Form::text('pagesize', $_pagesize, ['class' => 'form-control', 'style'=> 'width:60px;']) !!}
            <button type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> 搜索</button>
            <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
        </div>
        {!! Form::close() !!}

        @if ($items->total())
            {!! Form::open() !!}
            <table class="table mt8">
                <tr>
                    <th>用户ID</th>
                    <th>用户名</th>
                    <th>手机</th>
                    <th>邮箱</th>
                    <th>登录次数</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->username}}</td>
                        <td>{{$item->mobile}}</td>
                        <td>{{$item->email}}</td>
                        <td>{{$item->login_times}}</td>
                        <td>{{$item->created_at}}</td>
                        <td>
                            {{--
                            @if ($item->is_enable == 'Y')
                                <a class="J_request" data-toggle="tooltip" title="当前启用, 点击禁用"
                                   href="{{route_url('dsk_pam_account.status',null, ['id' => $item->id, 'field' => 'is_enable', 'status' => 'N'])}}">
                                    <i class="fa fa-unlock fa-lg text-success"></i>
                                </a>
                            @else
                                <a class="J_request " data-toggle="tooltip" title="当前禁用, 点击启用"
                                   href="{{route_url('dsk_pam_account.status',null, ['id' => $item->id, 'field' => 'is_enable', 'status' => 'Y'])}}">
                                    <i class="fa fa-lock fa-lg text-danger"></i>
                                </a>
                            @endif
                            --}}
                            {{--
                            <a data-toggle="tooltip" title="编辑[{{$item->account_name}}]"
                               href="{{route('dsk_pam_account.edit', [$item->id])}}">
                                <i class="fa fa-edit fa-lg"></i>
                            </a>
                            --}}
                            <a data-toggle="tooltip" title="修改密码" class="J_iframe"
                               href="{{route('backend:pam.password', [$item->id])}}">
                                <i class="glyphicon glyphicon-asterisk"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
            {!! Form::close() !!}
        <!-- 分页 -->
            <div class="mt10">
                {!! $items->render() !!}
            </div>
        @else
            @include('system::backend.tpl.inc_empty')
        @endif
    </div>
@endsection