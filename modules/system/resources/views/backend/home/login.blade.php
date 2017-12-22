<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">

    <title>Login V3 | Remark Admin Template</title>

    @include('lemon.inc.requirejs')

    <!-- Stylesheets -->
    {!! Html::style('assets/css/3rd/bt3.css') !!}
    {!! Html::style('assets/css/3rd/remark/bt3-extend.css') !!}
    {!! Html::style('assets/css/3rd/remark/site.css') !!}


    {!! Html::style('assets/css/3rd/remark/login-v3.css') !!}

</head>
<body class="page-login-v3 layout-full">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your
    browser</a> to improve your experience.</p>
<![endif]-->

<!-- Page -->
<div class="page vertical-align text-center" data-animsition-in="fade-in"
     data-animsition-out="fade-out">>
    <div class="page-content vertical-align-middle">
        <div class="panel">
            <div class="panel-body">
                <div class="brand">
                    <img class="brand-img" src="/project/lemon/images/default/logo.png" alt="..." style="width: 50px;">
                    <h2 class="brand-text font-size-18">Remark</h2>
                </div>

                {!! Form::open() !!}
                <div class="form-group form-material floating">
                    <input type="text" class="form-control" name="adm_name"/>
                    <label class="floating-label">用户名</label>
                </div>
                <div class="form-group form-material floating">
                    <input type="password" class="form-control" name="adm_pwd"/>
                    <label class="floating-label">密码</label>
                </div>
                <div class="form-group clearfix">
                    <div class="checkbox-custom checkbox-inline checkbox-primary checkbox-lg pull-left">
                        <input type="checkbox" id="inputCheckbox" name="remember">
                        <label for="inputCheckbox">记住我 ? </label>
                    </div>
                    <a class="pull-right" href="#">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg margin-top-40 J_submit">登录</button>
                {!! Form::close() !!}
                <p>Still no account? Please go to <a href="register-v3.html">Sign up</a></p>
            </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
            <p>WEBSITE BY amazingSurge</p>
            <p>© 2016. All RIGHT RESERVED.</p>
            <div class="social">
                <a class="btn btn-icon btn-pure" href="javascript:void(0)">
                    <i class="icon bd-twitter" aria-hidden="true"></i>
                </a>
                <a class="btn btn-icon btn-pure" href="javascript:void(0)">
                    <i class="icon bd-facebook" aria-hidden="true"></i>
                </a>
                <a class="btn btn-icon btn-pure" href="javascript:void(0)">
                    <i class="icon bd-google-plus" aria-hidden="true"></i>
                </a>
            </div>
        </footer>
    </div>
</div>
<script>
require(['jquery', 'lemon/cp', 'bt3'])
</script>
</body>
</html>