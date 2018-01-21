<script>
var appends = {!! json_encode(config('ext-fe.appends', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !!}
</script>
{!! Html::script('assets/js/libs/requirejs/require.js') !!}
{!! Html::script('assets/js/config.js') !!}