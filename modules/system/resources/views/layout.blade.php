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
    window.admin = "{{ url('admin') }}";
    window.api = "{{ url('api') }}";
    window.asset = "{{ asset('assets') }}";
    window.csrf_token = "{{ csrf_token() }}";
    window.domain = "{{ url('') }}";
    window.local = {!! $translations !!};
    window.monacoPath = "https://cdn.bootcss.com/monaco-editor/0.10.0/min/vs";
    window.token = "{{ url('admin/token') }}";
    window.upload = "{{ url('editor') }}";
    window.url = "{{ url('') }}";
    window.UEDITOR_HOME_URL = "https://src.neditor.notadd.com/0.1.0/";
</script>
<script src="{{ asset('assets/backend/js/app.min.js') }}"></script>
</body>