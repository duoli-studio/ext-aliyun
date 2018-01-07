@extends('lemon.template.shopnc')
@section('desktop-main')
    <div class="doc-container">
        @include('lemon.shopnc.header')
        <div class="doc-header">
            页面部分 <span class="intro">标题样式</span>
        </div>
        <h3 class="doc-title">固定在顶部的样式, 需要配合 div.page-fixed 使用</h3>
        <div class="mt10 clearfix">
            <div class="span-24">
                <div class="doc-emmet">
                    div.bar-fixed>div.title-bar>(h3{h3
                    title}+ul.tab-base>(li>a.current[href=#]>span{tab1})+(li>a[href=#]>span{tab2}))
                </div>
                <div class="doc-jsGen">
                    <div class="title-bar">
                        <h3>h3 title</h3>
                        <ul class="tab-base">
                            <li><a href="#" class="current"><span>tab1</span></a></li>
                            <li><a href="#"><span>tab2</span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <h3>h标题样式</h3>
        <div class="mt10 clearfix">
            <div class="span-12">
                <div class="doc-emmet">h1{h1标签，字号18，行高28，底部margin18,顶部margin18}</div>
                <div class="doc-jsGen"><h1>h1标签，字号18，行高28，底边距18，顶部margin18</h1></div>
            </div>
            <div class="span-12 last">
                <div class="doc-emmet">h2{h2标签，字号16，行高28，底部margin18,顶部margin18}</div>
                <div class="doc-jsGen"><h2>h2标签，字号16，行高28，底边距18，顶部margin18</h2></div>
            </div>
        </div>
        <div class="mt10 clearfix">
            <div class="span-12">
                <div class="doc-emmet">h3{h3标签，字号15，行高18，底部margin10}</div>
                <div class="doc-jsGen"><h3>h3标签，字号15，行高18，底边距10</h3></div>
            </div>
            <div class="span-12 last">
                <div class="doc-emmet">h4{h4标签，字号14，行高18，底部margin10}</div>
                <div class="doc-jsGen"><h4>h4标签，字号14，行高18，底边距10</h4></div>
            </div>
        </div>

        <div class="mt10 clearfix">
            <div class="span-12">
                <div class="doc-emmet">h5{h5标签，字号13，行高18，底部margin10}</div>
                <div class="doc-jsGen"><h5>h5标签，字号13，行高18，底边距10</h5></div>
            </div>
            <div class="span-12 last">
                <div class="doc-emmet">h6{h6标签，字号12，行高18，底部margin10}</div>
                <div class="doc-jsGen"><h6>h6标签，字号12，行高18，底边距10</h6></div>
            </div>
        </div>
        <h3>连接</h3>
        <div class="mt10 clearfix">
            <div class="span-6">
                <div class="doc-emmet">a.link-page{黑色连接}</div>
                <div class="doc-jsGen"><a class="link-page">黑色连接</a></div>
            </div>
            <div class="span-6">
                <div class="doc-emmet">a.link-hover{橘黄连接}</div>
                <div class="doc-jsGen"><a class="link-hover">橘黄连接</a></div>
            </div>

        </div>
        <div class="mt10 clearfix">
            <div class="span-6">
                <div class="doc-emmet">a.link-tips{橘黄按钮}</div>
                <div class="doc-jsGen"><a class="link-tips">橘黄按钮</a></div>
            </div>
            <div class="span-6">
                <div class="doc-emmet">a.link-do{蓝色按钮}</div>
                <div class="doc-jsGen"><a class="link-do">蓝色按钮</a></div>
            </div>
            <div class="span-6">
                <div class="doc-emmet">a.link-delete{红色连接}</div>
                <div class="doc-jsGen"><a class="link-delete">红色连接</a></div>
            </div>
        </div>
    </div>
@endsection