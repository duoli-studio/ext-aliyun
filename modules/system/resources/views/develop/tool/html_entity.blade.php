@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    <h4>Html实体转换器</h4>
    <div class="row mt10" style="font-family: monospace;">
        <div class="col-sm-6">
            <div class="alert alert-info">输入转换的代码</div>
            {!! Form::textarea('input', '<p>Sample Html String</p>', ['class'=> 'form-control', 'id'=> 'J_input', 'rows'=>10]) !!}
        </div>
        <div class="col-sm-6">
            <div class="alert alert-info">输出的代码</div>
            {!! Form::textarea('output', '', ['class'=> 'form-control', 'id'=> 'J_output', 'rows'=>10]) !!}
        </div>
    </div>
    <script>
	requirejs(['jquery', 'lemon/util'], function ($) {
		$(function () {
			$('#J_input').on('change mouseup input', _do_convert)
			_do_convert();
		});

		function _do_convert() {
			var $input  = $('#J_input');
			var $output = $('#J_output');
			var data    = $input.val();
			$.post('{!! route('system:develop.tool.html_entity') !!}', {content: data}, function (str) {
				$output.val(str.data.content);
			})
		}
	})
    </script>
@endsection