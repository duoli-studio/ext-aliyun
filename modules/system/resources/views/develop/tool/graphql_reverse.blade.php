@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    <h4>Graphql 去除特殊字符</h4>
    <div class="row" style="font-family: monospace;">
        <div class="col-sm-6">
            {!! Form::textarea('graphql', null, ['class'=> 'form-control', 'id'=> 'J_input']) !!}
        </div>
        <div class="col-sm-6">
            {!! Form::textarea('graphql_reverse', null, ['class'=> 'form-control', 'id' => 'J_output']) !!} <br>
            {!! Form::button('复制', ['id'=>'J_copy', 'class'=>'btn btn-info']) !!}
        </div>
    </div>
    <script>
	requirejs(['jquery', 'clipboard', 'poppy/util'], function ($, Clipboard, util) {
		$(function () {
			$('#J_input').on('change mouseup input', _do_convert);
			_do_convert();
		});

		function _do_convert() {
			var $input  = $('#J_input');
			var $output = $('#J_output');
			var data    = $input.val();
			$.post('{!! route('system:develop.tool.graphql_reverse') !!}', {content: data}, function (str) {
				$output.val(str.data.content);
			})
		}

		var clipboard = new Clipboard('#J_copy', {
			text: function (trigger) {
				return $(trigger).siblings('#J_output').val();
			}
		});
		clipboard.on('success', function () {
			util.splash({
				message: '复制成功',
				status : 0
			})
		});
		clipboard.on('error', function (e) {
			util.splash({
				message: '复制出错'
			});
		});
	})
    </script>
@endsection