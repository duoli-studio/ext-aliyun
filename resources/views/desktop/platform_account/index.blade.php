@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_account.header')
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::model(\Input::all(),['method' => 'get','class' => 'form-inline form-group-sm']) !!}
            <div class="form-group">
                {!! Form::label('platform', '平台') !!}
                {!! Form::select('platform', \App\Models\PlatformAccount::kvPlatform(), null, ['id'=>'platform', 'placeholder' => '所有平台', 'class' => 'form-control']) !!}
                {!! Form::label('note', '备注') !!}
                {!! Form::text('note', null, ['placeholder' => '备注', 'class' => 'form-control']) !!}
                {!! Form::label('contact', '发单人') !!}
                {!! Form::text('contact', null, ['placeholder' => '发单人', 'class' => 'form-control']) !!}
                {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
            </div>
            {!! Form::close() !!}
            @if ($items->total())
                <table class="table mt5">
                    <tr>
                        <th class="span-1">账号ID</th>
                        <th>游戏平台</th>
                        <th>平台账号</th>
                        <th>备注</th>
                        <th>发单人</th>
                        <th>联系电话</th>
                        <th>QQ</th>
                        <th>操作</th>
                    </tr>
                    @foreach($items as $item)
                        <tr class="border">
                            <td>
                                {!! $item->id !!}
                            </td>
                            <td>{!! \App\Models\PlatformAccount::kvPlatform($item->platform) !!}</td>
                            <td>
                                @if ($item->platform == \App\Models\PlatformAccount::PLATFORM_YI)
                                    {!! $item->yi_nickname!!}
                                @endif
                                @if ($item->platform == \App\Models\PlatformAccount::PLATFORM_MAO)
                                    {!! $item->mao_account !!}
                                @endif
                                @if ($item->platform == \App\Models\PlatformAccount::PLATFORM_MAMA)
                                    {!! $item->mama_account !!}
                                @endif
                                @if ($item->platform == \App\Models\PlatformAccount::PLATFORM_TONG)
                                    {!! $item->tong_nickname !!}
                                @endif
                                @if ($item->platform == \App\Models\PlatformAccount::PLATFORM_BAOZI)
                                    {!! $item->baozi_nickname !!}
                                @endif
                                @if ($item->platform == \App\Models\PlatformAccount::PLATFORM_YQ)
                                    {!! $item->yq_auth_key !!}
                                @endif
                            </td>
                            <td>{!! $item->note !!}</td>
                            <td>{!! $item->contact !!}</td>
                            <td>{!! $item->mobile !!}</td>
                            <td>{!! $item->qq !!}</td>
                            <td>
                                <a href="{!! route('dsk_platform_account.edit', [$item->id]) !!}">
                                    <i class="fa fa-edit text-success" data-toggle="tooltip" title="编辑"></i>
                                </a>
                                <a href="{!! route('dsk_platform_account.destroy', [$item->id]) !!}" class="J_request">
                                    <i class="fa fa-close text-danger" data-toggle="tooltip" title="删除"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                <!-- 分页 -->
                <div class="clearfix mt10">
                    <div class="pagination">
                        {!! $items->render() !!}
                    </div>
                </div>
            @else
                @include('desktop.inc.empty')
            @endif
        </div>
    </div>
@endsection