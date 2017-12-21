@extends('lemon.template.bt3')
@section('bootstrap-main')
	@include('lemon.inc.header')
	<div class="row mt10">
		<div class="col-sm-6">
			<div class="alert alert-info">输入转换的代码</div>
			<textarea name="input" id="J_input" class="form-control" rows="10"><p>Sample Html String</p></textarea>
		</div>
		<div class="col-sm-6">
			<div class="alert alert-info">输出的代码</div>
			<textarea name="output" id="J_output" class="form-control" rows="10"></textarea>
		</div>
	</div>
	<script>
	requirejs(['jquery', 'lemon/util'], function ($) {
		$(function () {
			$('#J_input').on('change mouseup input', _do_convert)
			_do_convert();
		});
		function _do_convert() {
			var $input = $('#J_input');
			var $output = $('#J_output');
			var data = $input.val();
			$.post('{!! url('tools/html-entity') !!}', {html : data}, function (str) {
				$output.val(str);
			})
		}
	})
	</script>
@endsection