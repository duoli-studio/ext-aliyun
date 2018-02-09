@component('mail::message')
# Test Mail

{{$content}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent