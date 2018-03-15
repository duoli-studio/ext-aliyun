@extends('slt::inc.tpl')
@section('tpl-main')
    <div class="container ">
        <div class="row">
            <div class="col-md-12">
                <div class="sour--my_title clearfix mt15">
                    <h3 class="pull-left">文档库</h3>
                    <div class="pull-right">
                        <a class="J_iframe btn btn-info btn-sm pull-right" data-width="400" data-height="444"
                           data-title="创建文库"
                           href="{!! route('web:prd.book') !!}">
                            <i class="iconfont icon-book-collection"></i>
                            创建文库
                        </a>
                    </div>
                </div>
                <div class="prd_my--category_ctr">
                    @if ($items->total())
                        <div class="row">
                            @foreach($items as $item)
                                <div class="col-sm-4">
                                    <div class="category_ctr-item clearfix">
                                        <div class="item-icon">
                                            <a href="{!! route('web:prd.my_book_item', [$item->id]) !!}"
                                               title="{!! $item->title !!}">
                                                <i class="iconfont icon-book-collection"></i>
                                            </a>
                                        </div>
                                        <div class="item-desc">
                                            <h4 class="text-ellipsis">
                                                <a href="{!! route('web:prd.show', [$item->id]) !!}"
                                                   title="{!! $item->title !!}">
                                                    {!! $item->title !!}
                                                </a>
                                            </h4>
                                            最后更新于 {!! \Sour\Lemon\Helper\TimeHelper::datetime($item->created_at, '3-2') !!}
                                            <div class="clearfix">
                                                <div class="item-more mr8">
                                                    <a class="J_iframe"
                                                       href="{!! route('web:prd.access', [$item->id]) !!}"
                                                       data-width="444" data-height="333" data-title="权限控制">
                                                        @if ($item->role_status == \Sour\Poppy\Models\PrdContent::ROLE_STATUS_PWD)
                                                            <i class="iconfont icon-lock"></i>
                                                        @else
                                                            <i class="iconfont icon-unlock"></i>
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="item-more mr8">
                                                    <a class="J_iframe"
                                                       href="{!! route('web:prd.address', [$item->id]) !!}"
                                                       data-width="444" data-height="555">
                                                        <i class="iconfont icon-share"></i>
                                                    </a>
                                                </div>
                                                <div class="btn-group item-more mr8">
                                                    <button type="button" class="dropdown-toggle" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                        <i class="iconfont icon-more-circle"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="J_iframe"
                                                               href="{!! route('web:prd.popup', [$item->id]) !!}">重命名</a>
                                                        </li>
                                                        @can('move', $item)
                                                            <li><a class="J_iframe"
                                                                   href="{!! route('web:prd.move', [$item->id]) !!}">移动</a>
                                                            </li>
                                                        @endcan
                                                        <li>
                                                            <a href="{!! route('web:prd.content', [$item->id]) !!}">编辑</a>
                                                        </li>
                                                        <li><a class="J_request"
                                                               data-confirm="确认删除`{!! $item->title !!}` ? "
                                                               href="{!! route('web:prd.status', [$item->id, 'delete']) !!}">删除</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($items->hasPages())
                            <div class="container clearfix">
                                <div class="pull-right">
                                    {!!$items->render()!!}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                @include('web.inc.empty')
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    @include('web.inc.footer')
@endsection