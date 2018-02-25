@extends('slt::tpl.default')
@section('tpl-main')
    <div class="container site--url">
        <div class="row">
            <div class="col-md-3">
                <div class="url-description url-box mt15">
                    <h3>发现墙是什么</h3>
                    <p>WuliHub的发现墙每天都会展示最好产品和新闻。这是一个分享最新APP，网站，硬件和技术、工具的好地方</p>
                    <div class="text-center mt8">
                        <a class="J_iframe btn btn-success url-share" href="{!! route('slt:nav.url') !!}"
                           data-width="600" data-height="480" @if(!empty($_pam))data-title="分享产品、工具、app、新闻、硬件或技术"
                           @else data-title="用户登录" @endif>分享好产品、网站或内容</a>
                    </div>
                    <p>
                        <span class="url-tip">小贴士</span><br>
                        登录后，在头像左侧的添加按钮可以创建自己的收藏夹和内容</p>
                    <p>
                        <span class="url-tip">QQ交流群 : 561913378</span> <br>
                        好产品分享和用户交流, 反馈意见及建议</p>
                </div>
                <div class="url-sponsors url-box">
                    <h4>Sponsors</h4>
                </div>
            </div>
            <div class="col-md-9">
                <div class="url-labels mt15">
                    @foreach($tags as $tag)
                        <a href="?tag={!! \Input::get('tag') ? \Input::get('tag').'|' : '' !!}{!! $tag['title'] !!}"># {!! $tag['title'] !!}</a>
                    @endforeach
                </div>
                <div class="url-display" id="infinite_scroll">
                    <div class="url-infinite">
                        <div class="url-display_head clearfix">
                            <h3>收藏夹</h3>
                            {!! Form::open(['class' => 'pull-right']) !!}
                            {!! Form::text('kw', \Input::get('kw'), ['placeholder'=> '搜索内容...', 'class'=> 'form-control input-sm']) !!}
                            {!! Form::close() !!}
                        </div>
                        <div class="url-display_content"
                             @if (!\Input::get('last_date')) id="suggest_content" @endif>
                            @if(!empty($items))
                                @foreach($items as $item)
                                    <div class="clearfix display_content-ctr">
                                        <a target="_blank" href="{!! $item->siteUrl->url !!} "
                                           class="display_content-wrapper clearfix">
                                            <div class="pull-left">
                                                <span class="display_content-image">
                                                    <span class="display_content-title">
                                                        {!! slt_image($item->siteUrl->icon,null, ['width' => 118, 'height'=> 70, 'class'=> 'pull-left']) !!}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="pull-right">
                                                <div class="display_content-top clearfix">
                                                    <div class="pull-left">
                                                        <span class="display_content-title">
                                                            {!! $item->title !!}
                                                        </span>
                                                        <p class="display_content-desc">
                                                            {!!  $item->siteUrl->description !!}
                                                        </p>
                                                    </div>
                                                    @if($item->account_front)
                                                        <p class="pull-right">
                                                            {!! Html::image($item->account_front->head_pic, $item->account_front->nickname, [
                                                                'width' =>28, 'height' => 28, 'class' => 'img-circle mr5'
                                                            ]) !!}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                        <div class="display_content-links">
                                            <p class="pull-left display_content-tag">
                                                {!! \Slt\Models\SiteUrlRelTag::translate($item->siteUrlRelTag) !!}
                                            </p>
                                            <p class="pull-right">
                                                <a data-title="点赞" class="display_content-comment good btn btn-default">
                                                    <i class="iconfont icon-good"></i>
                                                    <span class="j_num">{!! $item->good_num !!}</span>
                                                </a>
                                            </p>
                                        </div>

                                    </div>
                                @endforeach
                                @if( method_exists($items, 'render'))
                                    <div class="pull-right">
                                        {!! $items->render() !!}
                                    </div>
                                @endif
                            @else
                                <div class="url-empty_content j_scroll_content">
                                    暂时没有链接
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection