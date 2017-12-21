@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        欢迎界面
        <div class="pull-right">
            <div class="btn-group">
                <a href="#"  class="btn btn-primary J_request">更新缓存</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">感谢您使用游戏代练管理系统</div>
                </div>
                <div class="panel-body">
                    您现在使用的是一套用于外包业务的管理系统, 如果您有任何疑问请点左下角的QQ进行咨询,
                    此程序操作简单, 支持先进浏览器, 采用ajax交互先进技术, 在先进的浏览器上有更佳的性能体验
                    <ul>
                        <li>本程序由 Mark Zhao 全新制作</li>
                        <li>本程序仅提供使用</li>
                        <li>支持作者的劳动</li>
                        <li>程序使用, 技术支持, 请联系: QQ: 408128151</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">进度更新</div>
                </div>
                <div class="panel-body">
                    {!! $html  !!}
                </div>
            </div>
        </div>
    </div>
@endsection
