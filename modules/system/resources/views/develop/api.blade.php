@extends('system::tpl.develop')
@section('develop-main')
    <nav class="navbar navbar-default">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">
                样式/功能入口
            </a>
        </div>
    </nav>
    <div class="row">
        <div class="col-sm-12" style="word-break: break-all;">
            @if ($token)
                <p class="alert alert-success">
                    已经设置 token
                    {!! $token !!} <br><br>
                    <a target="_blank" href="{!! $graphql_view !!}">未授权</a>
                    <a target="_blank" href="{!! $graphql_auth_view !!}">授权访问</a>
                </p>
            @endif
        </div>
        <div class="col-sm-6">
            {!! Form::open(['url'=> $token_url, 'id'=> 'form_auto']) !!}
            @if (!$token)
                <p class="alert alert-warning">
                    当前没有 Access Token , 登录自动设置
                </p>
            @endif
            <div class="form-group">
                <label for="username">账号</label>
                ( String ) [username]
                <input class="form-control" name="username" type="text" id="username">
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                ( String ) [password]
                <input class="form-control" name="password" type="text" id="password">
            </div>
            <div class="form-group">
                <button class="btn btn-info" type="submit" id="submit">登录</button>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">
                <input class="form-control" readonly="1" name="url" type="text"
                       value="{!! $token_url !!}" id="url">
            </div>
            <pre id="J_result" style="display: none;margin-top:20px;"></pre>
        </div>
    </div>
    <script>
		requirejs(['jquery', 'json', 'poppy/util', 'js-cookie', 'jquery.validation'], function($, JSON, util, Cookies) {
			$(function() {
				var conf = util.validate_config({
					    submitHandler : function(form) {
						    $('#J_result').text(
							    '进行中...'
						    ).css('color', 'grey');
						    $(form).ajaxSubmit({
							    beforeSend : function(request) {
								    request.setRequestHeader("Accept", "application/x..v1+json");
							    },
							    success    : function(data) {
								    var objData = util.to_json(data);
								    if (typeof objData.data != 'undefined') {
									    var objSubData = objData.data;
									    Cookies.set('{!! $cookie_key !!}', objSubData);
									    window.location.reload();
								    }

								    $('#J_result').text(
									    JSON.stringify(util.to_json(data), null, '  ')
								    ).show(300).css('color', 'green');
							    },
							    error      : function(data) {
								    var resp = util.to_json(data.responseText);
								    $('#J_result').text(
									    JSON.stringify(resp, null, '  ')
								    ).show(300).css('color', 'red');
							    }
						    });
					    },
					    'rules'       : {
						    username : {
							    required : false
						    },
						    password : {
							    required : true
						    }
					    }
				    }, 'bt3_ajax')
				;
				$('#form_auto').validate(conf);
			});

		})
    </script>

@endsection