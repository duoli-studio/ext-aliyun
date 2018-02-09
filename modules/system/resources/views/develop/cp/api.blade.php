@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    <div class="row">
        <div class="col-sm-12" style="word-break: break-all;">
            <p class="alert alert-info">
                <a target="_blank" class="btn btn-default" href="{!! route('system:develop.cp.graphql', 'default') !!}">
                    默认访问
                </a>
                <a target="_blank" class="btn btn-info J_iframe"
                   href="{!! route_url('system:develop.cp.set_token', [], ['type'=> 'default']) !!}">
                    手工设置 Token
                </a>
                token :
                @if ($token_default)
                    {!! $token_default !!}
                @else
                    无
                @endif
            </p>
            <p class="alert alert-warning">
                <a target="_blank" class="btn btn-default" href="{!! route('system:develop.cp.graphql', 'web') !!}">
                    前台访问
                </a>
                <a target="_blank" class="btn btn-info J_iframe"
                   href="{!! route_url('system:develop.cp.api_login', [], ['type'=> 'web']) !!}">
                    登录获取 Token
                </a>
                token :
                @if ($token_web)
                    {!! $token_web !!}
                @else
                    无
                @endif
            </p>
            <p class="alert alert-danger">
                <a target="_blank" class="btn btn-default" href="{!! route('system:develop.cp.graphql', 'backend') !!}">
                    后台访问
                </a>
                <a target="_blank" class="btn btn-info J_iframe"
                   href="{!! route_url('system:develop.cp.api_login', [], ['type'=> 'backend']) !!}">
                    登录获取 Token
                </a>
                token :
                @if ($token_backend)
                    {!! $token_backend !!}
                @else
                    无
                @endif
            </p>
            <p class="alert alert-success">
                <a target="_blank" class="btn btn-default" href="{!! $url_system !!}">
                    Restful 接口文档
                </a>
                <a target="_blank" class="btn btn-default" href="{!! $url_poppy !!}">
                    Poppy 后端文档
                </a>
            </p>
        </div>
    </div>
@endsection