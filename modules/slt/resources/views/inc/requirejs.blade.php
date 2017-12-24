<script>
	var appends = {
		slt   : '{!! config('app.url') !!}/modules/slt/js/',
		poppy : '{!! config('app.url') !!}/assets/js/poppy/'
	}
</script>
{!! Html::script('assets/js/require.js') !!}
{!! Html::script('assets/js/config.js') !!}