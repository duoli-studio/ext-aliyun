@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.pam_account.header')
    <div class="panel panel-default">
        <div class="panel-body">
            @if (isset($item))
                {!! Form::model($item,['route' => ['dsk_pam_account.edit', $item['account_id']], 'id' => 'form_item', 'class' => 'form-horizontal']) !!}
            @else
                {!! Form::open(['route' => 'dsk_pam_account.create','id' => 'form_item', 'class' => 'form-horizontal']) !!}
            @endif
            {!!Form::hidden('account_type', $account_type)!!}

            <div class="form-group">
                {!! Form::label('account_name', '用户名', ['class' => 'strong validation col-lg-2 control-label']) !!}
                <div class="col-lg-2">
					<?php
					$options = [
						'class' => 'form-control'
					];
					if (isset($item)) {
						$options['readonly'] = 'readonly';
						$options['disabled'] = 'disabled';
					}
					?>
                    {!! Form::text('account_name', null, $options) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('password', '密码', ['class' => 'col-lg-2 control-label strong '.(!isset($item) ? 'validation' : '')]) !!}
                <div class="col-lg-2">
                    {!! Form::password('password', ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('password_confirmation', '重复密码', ['class' => 'col-lg-2 control-label strong '.(!isset($item) ? 'validation' : '')]) !!}
                <div class="col-lg-2">
                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('role_id', '用户角色', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::select('role_id', $roles,  ['class' => 'form-control']) !!}
                </div>
            </div>

            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DESKTOP)

                <div class="form-group">
                    {!! Form::label('desktop[qq]', 'QQ', ['class' => 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('desktop[qq]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('desktop[mobile]', '联系方式', ['class' => 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('desktop[mobile]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('desktop[realname]', '真实姓名', ['class' => 'col-lg-2 control-label strong']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('desktop[realname]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
            @endif

            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_FRONT)
                <div class="form-group">
                    {!! Form::label('payword', '支付密码', ['class' => 'col-lg-2 control-label strong '.(!isset($item) ? 'validation' : '')]) !!}
                    <div class="col-lg-2">
                        {!! Form::password('front[payword]', ['id' => 'payword','class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('payword_confirmation', '重复支付密码', ['class' => 'col-lg-2 control-label strong '.(!isset($item) ? 'validation' : '')]) !!}
                    <div class="col-lg-2">
                        {!! Form::password('front[payword_confirmation]',['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('front[qq]', 'QQ', ['class' => 'col-lg-2 control-label strong']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('front[qq]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('front[mobile]', '联系方式', ['class' => 'col-lg-2 control-label strong']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('front[mobile]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('front[address]', '地址', ['class' => 'col-lg-2 control-label strong']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('front[address]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
            @endif
            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DEVELOP)

                <div class="form-group">
                    {!! Form::label('develop[nickname]', '虚拟名称', ['class' => 'col-lg-2 control-label strong place']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('develop[nickname]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('develop[truename]', '真实姓名', ['class' => 'col-lg-2 control-label strong place']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('develop[truename]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('develop[email]', '邮箱地址', ['class' => 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('develop[email]',null,['class' => 'form-control']) !!}
                    </div>
                </div>
            @endif
            <div class="form-group">
                {!! Form::label('', '', ['class' => 'col-lg-2 control-label strong']) !!}
                <div class="col-lg-2">
                    {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                </div>
            </div>
            {!! Form::close() !!}
            <script>
				require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
					var conf = util.validate_conf({
						rules   : {
                            @if (!isset($item))
							account_name                 : {
								required: true,
								remote  : "{{route('support_validate.account_name_available')}}"
							},
                            @endif
							password                     : {
                                @if (!isset($item)) required: true @endif
							},
							password_confirmation        : {
                                @if (!isset($item)) required: true, @endif
								equalTo                     : '#password'
							}
                            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DESKTOP)
							,
							'desktop[qq]'                : {qq: true},
							'desktop[mobile]'            : {mobile: true}
                            @endif
                            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_FRONT)
							,
							'front[qq]'                  : {qq: true},
							'front[mobile]'              : {mobile: true},
							'front[payword]'             : {},
							'front[payword_confirmation]': {equalTo: '#payword'}
                            @endif
                            @if ($account_type == \App\Models\PamAccount::ACCOUNT_TYPE_DEVELOP)
							,
							'develop[email]'             : {
								email   : true,
								required: true
							}
                            @endif
						},
						messages: {
							account_name: {
								required: '此项必填',
								remote  : "账户名重复, 请重新填写"
							}
						}
					}, 'bt3');
					$('#form_item').validate(conf);
				});
            </script>
        </div>
    </div>




@endsection