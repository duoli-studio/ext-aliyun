<p class="alert alert-info" id="introduce">
    输入主机的链接信息, 并提交, 链接成功之后可以收到主机返回的信息
</p>
{!! Form::model(\Input::all(), ['class'=> 'form-horizontal']) !!}
<div class="form-group">
    {!! Form::label('url', 'Host', ['class'=> 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('url', null, ['class'=> 'form-control', 'placeholder'=>'请输入需要连接的主机名称']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('user', 'user', ['class'=> 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('user', null, ['class'=> 'form-control', 'placeholder'=>'用户ID']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('timestamp', 'timestamp', ['class'=> 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('timestamp', null, ['class'=> 'form-control', 'placeholder'=>'timestamp']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('token', 'token', ['class'=> 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('token', null, ['class'=> 'form-control', 'placeholder'=>'输入连接 token']) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('channel', 'channel', ['class'=> 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('channel', null, ['class'=> 'form-control', 'placeholder'=>'输入订阅频道']) !!}
    </div>
</div>
<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        {!! Form::button('提交', ['class'=> 'btn btn-info', 'type'=> 'submit']) !!}
    </div>
</div>
{!! Form::close() !!}
<hr>
@if (is_post())
    <div class="row" id="code">
        <div class="col-md-12">
            <h4>代码示例</h4>
            <pre id="J_content">

        </pre>
            <pre id="J_script"></pre>
        </div>
    </div>
    <script id="J_scriptSource">
	require(['centrifuge', 'json', 'sockjs'], function(Centrifuge, JSON) {
		var centrifuge = new Centrifuge({
			url       : "{!! \Input::get('url') !!}",
			user      : "{!! \Input::get('user') !!}",
			timestamp : "{!! \Input::get('timestamp') !!}",
			token     : "{!! \Input::get('token') !!}"
		});

		centrifuge.subscribe("{!! \Input::get('channel') !!}", function(message) {
			var $content = $('#J_content');
			$content.html(JSON.stringify(message) + '<br>' + $content.html());
		});

		centrifuge.connect();
	})
    </script>
@endif