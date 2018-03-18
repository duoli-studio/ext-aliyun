<!DOCTYPE html>
<html>
<head>
    <title>title</title>
    <meta charset="utf-8">
    <meta name="description" content="description">
    <meta name="keyword" content="keywords">
    <link href="{{ asset('assets/backend/css/app.min.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <img src="{{ asset('assets/backend/images/loading.svg') }}"
         style="position: absolute;
             background: #fff;
             height: 80%;
             left: 50%;
             top: 50%;
             -moz-transform: translate(-50%, -50%);
             -o-transform: translate(-50%, -50%);
             -webkit-transform: translate(-50%, -50%);
             transform: translate(-50%, -50%);">
</div>
<script>
window.admin = "{{ url('system') }}";
window.api = "{{ url('api_v1') }}/";
window.asset = "{{ asset('assets') }}";
window.csrf_token = "{{ csrf_token() }}";
window.domain = "{{ url('') }}";
window.local = {!! $translations !!};
window.monacoPath = "https://cdn.bootcss.com/monaco-editor/0.10.0/min/vs";
window.token = "{{ url('system/token') }}";
window.upload = "{{ url('system:util.image.upload') }}";
</script>
<script src="{{ asset('assets/backend/js/app.min.js') }}"></script>
</body>