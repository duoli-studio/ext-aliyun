@extends('system::backend.tpl.default')
@section('head-css')
    {!! Html::style('assets/css/basic.css') !!}
    {!! Html::style('assets/css/backend.css') !!}
@endsection
@section('body-class', 'gray-bg')
@section('body-main')
    @if(isset($input))
        {!!  Session::flashInput($input) !!}
    @endif
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="panel @if (Session::get('end.level') == 0 )  panel-success @else panel-warning  @endif">
                    <div class="panel-heading">
                        @if (Session::get('end.level') == 0 )
                            <h3 class="panel-title"><i class="fa fa-check-circle-o"></i> 操作成功</h3>
                        @endif
                        @if (Session::get('end.level') == 1 )
                            <h3 class="panel-title"><i class="fa fa-times-circle-o"></i> 操作失败</h3>
                        @endif
                    </div>
                    <div class="panel-body">
                        <p>{!! Session::get('end.message') !!}</p>
                        <p>
                            @if (isset($location))
                                @if ($location == 'back' || $time == 0)
                                    @if ($location != 'message')
                                        <a href="javascript:window.history.go(-1);">返回上级</a>
                                    @endif
                                @else
                                    您将在 <span id="clock">0</span>秒内跳转至目标页面, 如果不想等待, <a
                                            href="{!! $location !!}">点此立即跳转</a>!
                                    <script>
										require(['jquery'], function($) {
											$(function() {
												var t = {!! $time !!};//设定跳转的时间
												setInterval(refer(), 1000); //启动1秒定时
												function refer() {
													if (t == 0) {
														window.location.href = "{!! $location !!}"; //设定跳转的链接地址
													}
													$('#clock').text(Math.ceil(t / 1000)); // 显示倒计时
													t -= 1000;
												}
											})
										})
                                    </script>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection