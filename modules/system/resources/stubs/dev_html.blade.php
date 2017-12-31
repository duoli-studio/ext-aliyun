<!DOCTYPE html>
<html>
<head>
    <title>{!! $site_name !!}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<div id="app"></div>
<script>
	window.url = "{!! $url !!}";
	window.api = url + "/api";
	window.asset = url + "/assets";
	window.admin = "http://127.0.0.1:8080/";
	window.local = {!! $translations !!};
	window.monacoPath = "https://cdn.bootcss.com/monaco-editor/0.10.0/min/vs";
	window.modules = [
		"notadd/content",
		"notadd/member",
	];
	window.token = url + "/admin/token";
	window.upload = url + "/editor";

	window.UEDITOR_HOME_URL = "/assets/neditor/";
</script>
</body>
</html>
