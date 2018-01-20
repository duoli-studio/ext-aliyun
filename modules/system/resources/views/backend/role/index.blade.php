@extends('system::backend.tpl.default')
@section('backend-breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-8">
            <h2>角色管理</h2>
            <ol class="breadcrumb">
                <li> 管理项目中角色</li>
            </ol>
        </div>
        <div class="col-lg-4">
            <div class="title-action pull-right">
                <div class="btn-group" role="group">
                    @can('create', \System\Models\PamRole::class)
                        <a href="{{route_url('backend:role.establish', [], [
                            'type' => $type,
                        ])}}" class="btn btn-default J_iframe">创建角色</a>
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
    <table class="table mt15">
        <tr>
            <th>ID</th>
            <th>角色</th>
            <th>角色显示名称</th>
            <th>操作</th>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->title}}</td>
                <td>
                    @can('menu', $item)
                        <a class="glyphicon glyphicon-cog J_iframe"
                           data-title="编辑 [{{$item->title}}] 权限"
                           data-width="600"
                           href="{{route('backend:role.menu', [$item->id])}}"></a>
                    @endcan
                    @can('edit', $item)
                        <a class="glyphicon glyphicon-edit J_iframe" href="{{route('backend:role.establish', [$item->id])}}"></a>
                    @endcan
                    @can('delete', $item)
                        <a class="glyphicon glyphicon-remove text-danger J_request"
                           data-confirm="确认删除角色 `{!! $item->title !!}`?"
                           href="{{route('backend:role.delete', [$item->id])}}"></a>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    {!! $items->appends(Input::except('page'))->render() !!}
@endsection