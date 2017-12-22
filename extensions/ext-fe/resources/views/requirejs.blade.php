<script>
var appends = {!! json_encode(config('ext-fe.appends')) !!} ;
</script>
{!! Html::script('assets/js/require.js') !!}
{!! Html::script('assets/js/config.js') !!}