@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_account.header')
    <div class="panel panel-default">
        <div class="panel-body">
            @if (isset($item))
                {!! Form::model($item,['route' => ['dsk_platform_account.edit', $item->id], 'id' => 'form_account','class'=> 'form-horizontal']) !!}
            @else
                {!! Form::open(['route' => 'dsk_platform_account.create','id' => 'form_account','class'=> 'form-horizontal']) !!}
            @endif
            @if ($_route != 'dsk_platform_account.edit')
                <ul class="nav nav-tabs">
                    <li @if ($type == App\Models\PlatformAccount::PLATFORM_YI )   class="active" @endif>
                        <a href="{!! route('dsk_platform_account.create', [\App\Models\PlatformAccount::PLATFORM_YI]) !!}"><span>易代练</span></a>
                    </li>
                    <li @if ($type == App\Models\PlatformAccount::PLATFORM_MAO )   class="active" @endif>
                        <a href="{!! route('dsk_platform_account.create', [\App\Models\PlatformAccount::PLATFORM_MAO]) !!}"><span>代练猫</span></a>
                    </li>
                    <li @if ($type == App\Models\PlatformAccount::PLATFORM_MAMA )   class="active" @endif>
                        <a href="{!! route('dsk_platform_account.create', [\App\Models\PlatformAccount::PLATFORM_MAMA]) !!}"><span>代练妈妈</span></a>
                    </li>
                    <li @if ($type == App\Models\PlatformAccount::PLATFORM_TONG )   class="active" @endif>
                        <a href="{!! route('dsk_platform_account.create', [\App\Models\PlatformAccount::PLATFORM_TONG]) !!}"><span>代练通</span></a>
                    </li>
                    <li @if ($type == App\Models\PlatformAccount::PLATFORM_YQ )   class="active" @endif>
                        <a href="{!! route('dsk_platform_account.create', [\App\Models\PlatformAccount::PLATFORM_YQ]) !!}"><span>17代练</span></a>
                    </li>
                    <li @if ($type == App\Models\PlatformAccount::PLATFORM_BAOZI )   class="active" @endif>
                        <a href="{!! route('dsk_platform_account.create', [\App\Models\PlatformAccount::PLATFORM_BAOZI]) !!}"><span>电竞包子</span></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            @endif
            <div class="pt10">
                <div class="form-group">
                    {!! Form::label('contact', '发单联系人:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('contact', null,['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('mobile', '手机:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('mobile', null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('qq', 'QQ:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::text('qq', null,['class' => 'form-control']) !!}
                    </div>
                </div>

                @if ($type == App\Models\PlatformAccount::PLATFORM_YI)
                    {!! Form::hidden('type', App\Models\PlatformAccount::PLATFORM_YI) !!}
                    <div class="form-group">
                        {!! Form::label('note', '易代练用户ID:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yi_userid', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '易代练昵称:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yi_nickname', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '易代练key:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yi_app_key', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '易代练secret:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yi_app_secret', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '支付密码:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yi_payword', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '备注:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('note', null,['class' => 'form-control']) !!}
                        </div>
                    </div>

                @endif
                @if ($type == App\Models\PlatformAccount::PLATFORM_BAOZI)
                    {!! Form::hidden('type', App\Models\PlatformAccount::PLATFORM_BAOZI) !!}
                    <div class="form-group">
                        {!! Form::label('note', '电竞包子用户ID:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('baozi_userid', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '电竞包子昵称:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('baozi_nickname', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '电竞包子key:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('baozi_app_key', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '电竞包子secret:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('baozi_app_secret', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '支付密码:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('baozi_payword', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '备注:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('note', null,['class' => 'form-control']) !!}
                        </div>
                    </div>

                @endif
                @if ($type == App\Models\PlatformAccount::PLATFORM_MAO)
                    {!! Form::hidden('type', App\Models\PlatformAccount::PLATFORM_MAO) !!}

                    <div class="form-group">
                        {!! Form::label('note', '代练猫key:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('mao_account', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练猫secret:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('mao_password', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练猫支付密码:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('mao_payword', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '备注:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('note', null,['class' => 'form-control']) !!}
                        </div>
                    </div>
                @endif
                @if ($type == App\Models\PlatformAccount::PLATFORM_MAMA)
                    {!! Form::hidden('type', App\Models\PlatformAccount::PLATFORM_MAMA) !!}

                    <div class="form-group">
                        {!! Form::label('note', '代练妈妈key:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('mama_account', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练妈妈secret:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('mama_password', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练妈妈支付密码:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('mama_payword', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '备注:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('note', null,['class' => 'form-control']) !!}
                        </div>
                    </div>
                @endif
                @if ($type == App\Models\PlatformAccount::PLATFORM_TONG)
                    {!! Form::hidden('type', App\Models\PlatformAccount::PLATFORM_TONG) !!}

                    <div class="form-group">
                        {!! Form::label('note', '代练通昵称:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('tong_nickname', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练通 USERID:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('tong_userid', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练通 AccessKey:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('tong_account', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '代练通 Secret:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('tong_password', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '支付密码:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::tip('为了避免密码外泄, 这里可以填写 md5 后的密码值') !!}
                            {!! Form::text('tong_payword', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '备注:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('note', null,['class' => 'form-control']) !!}
                        </div>
                    </div>

                @endif
                @if ($type == App\Models\PlatformAccount::PLATFORM_YQ)
                    {!! Form::hidden('type', App\Models\PlatformAccount::PLATFORM_YQ) !!}

                    <div class="form-group">
                        {!! Form::label('note', 'phone:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yq_phone', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', 'authKey:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yq_auth_key', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', 'appId:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yq_account', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', '支付密码:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yq_payword', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('note', 'userid:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('yq_userid', null, ['data-rule-required' => 'true','class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('note', '备注:', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                        <div class="col-lg-2">
                            {!! Form::text('note', null,['class' => 'form-control']) !!}
                        </div>
                    </div>

                @endif
                <div class="form-group">
                    {!! Form::label('', '', ['class'=> 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::button((isset($item) ? '编辑' : '创建'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <script>
			requirejs(['jquery', 'lemon/util', 'jquery.validate'], function ($, util) {
				$('#form_account').validate(util.validate_conf({
					rules: {
						note   : {required: true},
						mobile : {
							required: true,
							mobile  : true
						},
						qq     : {
							required: true,
							qq      : true
						},
						contact: {required: true}
					}
				}, 'dsk_ajax'));
			})
            </script>
        </div>
    </div>
@endsection