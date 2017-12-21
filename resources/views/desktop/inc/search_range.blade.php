<input id="J_daystart" class="form-control" name="start_date" value="{!! Input::get('start_date', isset($start_date) ? $start_date : '') !!}" autocomplete="off" type="text" placeholder="开始时间"> -
<input id="J_dayend" class="form-control" name="end_date" value="{!! Input::get('end_date', isset($end_date) ? $end_date : '') !!}" autocomplete="off" type="text" placeholder="结束时间">
<script>
require(['jquery', 'jquery.ui'], function ($) {
	$(function () {
		$("#J_daystart").datepicker({
			onClose    : function (selectedDate) {
				$("#J_dayend").datepicker("option", "minDate", selectedDate);
			},
			dateFormat : "yy-mm-dd"
		});
		$("#J_dayend").datepicker({
			maxDate    : 0,
			onClose    : function (selectedDate) {
				$("#J_daystart").datepicker("option", "maxDate", selectedDate);
            },
			dateFormat : "yy-mm-dd"
		});
	})
})
</script>