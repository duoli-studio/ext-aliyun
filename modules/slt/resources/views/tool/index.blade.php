@extends('slt::inc.tpl')
@section('body-class', 'tool--editor')
@section('tpl-main')
    <div class="container mb20 mt20">
        <div class="row">
            <div class="col-sm-3">
                <ul class="list-group">
                    <li class="list-group-item {!! $type == 'xml' ? 'active' : '' !!}">
                        <a href="{!! route('slt:tool', ['xml']) !!}">Xml 格式化</a>
                    </li>
                    <li class="list-group-item {!! $type == 'json' ? 'active' : '' !!}">
                        <a href="{!! route('slt:tool', ['json']) !!}">Json 格式化</a>
                    </li>
                    <li class="list-group-item {!! $type == 'sql' ? 'active' : '' !!}">
                        <a href="{!! route('slt:tool', ['sql']) !!}">Sql 格式化</a>
                    </li>
                    <li class="list-group-item {!! $type == 'css' ? 'active' : '' !!}">
                        <a href="{!! route('slt:tool', ['css']) !!}">Css 格式化</a>
                    </li>
                </ul>
            </div>
            <div class="col-sm-9">
                <div class="panel panel-default">
                    <div class="panel-heading">{!! ucfirst($type) !!} 格式化</div>
                    <div class="panel-body editor-wrapper">
                        <pre id="editor" class="editor-main_area"></pre>
                        <p class="editor-handle">
                            <button id="format" class="btn btn-sm btn-info">格式化</button>
                            <button id="copy" data-clipboard-target="#editor" class="btn btn-sm btn-info">复制</button>
                            <button id="minify" class="btn btn-sm btn-warning">压缩</button>
                            <button id="clear" class="btn btn-sm btn-danger">清空</button>
                        </p>
                        <script>
                            require(['jquery', 'ace/ace', 'clipboard', 'poppy/util', 'vkbeautify'],
                                function ($, ace, Clipboard, poppy, vkbeautify) {
                                    $(function () {
                                        var editor = ace.edit("editor");
                                        editor.setTheme("ace/theme/chrome");
                                        editor.session.setMode("ace/mode/{!! $type !!}");

                                       $("#copy").click(function () {
	                                        new Clipboard('#copy', {
		                                        text: function() {
			                                        return editor.getValue();
		                                        }
	                                        });
	                                        poppy.splash({
		                                        status : 0,
		                                        message: '已经复制到粘贴板'
	                                        });
                                        });

                                        $("#format").click(function () {
                                            var content = editor.getValue();
                                            try {
                                                editor.setValue(vkbeautify.{!! $type !!}(content));
                                            } catch (err) {
                                                alert("Your document is invalid");
                                            }
                                        });
                                        $("#clear").click(function () {
                                            editor.setValue("");
                                        });
                                        $("#minify").click(function () {
                                            var content = editor.getValue();
                                            try {
                                                editor.setValue(vkbeautify.{!! $type !!}min(content));
                                            } catch (err) {
                                                alert("Your document is invalid");
                                            }
                                        });
                                    })
                                }
                            );
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('slt::inc.footer')
@endsection