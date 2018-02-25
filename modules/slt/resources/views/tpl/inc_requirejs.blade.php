<script>
var appends = {!! json_encode(config('ext-fe.appends', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !!}
</script>
{!! Html::script('resources/js/libs/requirejs/require.js') !!}
{!! Html::script('resources/js/config.js') !!}