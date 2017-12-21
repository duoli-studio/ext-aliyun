@extends('web.inc.tpl_dialog')
@section('body-dialog_class') site_collection @endsection
@section('tpl-main')
    {!! Form::model(isset($item)?$item: null,['route' => ['web:nav.url', isset($item)?$item->id: null]]) !!}
    <div class="form-group">
        <label>网站地址</label>
        {!! Form::text('url', $url, ['placeholder' => '输入收藏的网站地址', 'class'=>'form-control', 'id'=> 'url']) !!}
    </div>
    <div class="form-group" id="site_title">
        <label>网站标题</label>
        {!! Form::text('title', $title, ['placeholder' => '输入网站标题', 'class'=>'form-control', 'id'=> 'title']) !!}
    </div>
    <div class="form-group">
        <label>描述</label>
        {!! Form::textarea('description', $description, ['placeholder' => '输入网站描述, 网站描述不得超过80字符(#)', 'class'=>'form-control','rows'=>3]) !!}
    </div>
    <div class="form-group">
        <label>标签</label>
        <div class="clearfix">
            {!! Form::select('tag[]',[], [], ['id'=> 'site_tags' , 'multiple'=> 'multiple']) !!}
        </div>
    </div>
    <div class="form-group">
        <label>标签</label>
        <div class="clearfix">
            {!! Form::webuploader('icon',$icon, ['id'=> 'icon', 'ext'=>'image']) !!}
        </div>
    </div>
    <div class="form-group clearfix">
        {!! Form::button('添加网址', ['type'=> 'submit', 'class' => 'btn btn-success form-control J_submit']) !!}
    </div>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.tokenize2'], function ($, util) {
            function get_title() {
                var url = $('#url').val();
                if (!url || $('#title').val()) {
                    return;
                }
                util.make_request("{!! route('web:nav.title') !!}", {url: url}, function (resp_obj) {
                    var obj_data = resp_obj.data;
                    if (resp_obj.status == 0) {
                        $('#title').val(obj_data.title);
                        $('#url').val(obj_data.url);
                        $('#site_title').fadeIn(500);
                    } else {
                        $('#title').val(obj_data.title);
                        $('#url').val(obj_data.url);
                        $('#site_title').fadeIn(500);
                        util.splash(resp_obj);
                    }
                })
            }

            $('#url').on('blur', get_title);
            $(function () {
                get_title();
            });
            $('#site_tags').tokenize2({
                dataSource       : '{!! route('web:nav.tag') !!}',
                tokensAllowCustom: true,
                placeholder      : '输入新标签或者点选标签, 最多 6 个',
                tokensMaxItems   : 6,
                searchMinLength  : 2,
                searchHighlight  : true,
                debounce         : 800
            });
        })
    </script>
@endsection