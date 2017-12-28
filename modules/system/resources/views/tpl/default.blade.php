<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@if (isset($_title) && $_title) {!! $_title !!} - @endif</title>
    @yield('head-meta')
    @yield('head-css')
    @yield('head-script')
</head>
<body class="@yield('body-class')">

@yield('body-main')

@yield('footer-script')
</body>
</html>