@extends('lemon.template.bt3')
@section('bootstrap-main')
    @include('develop.up.nav')
    <div class="row mt10">
        <div class="col-sm-6">
            {!! Form::model($data,['id'=> 'form_login']) !!}
            @if(isset($data['up_user']))
                <p class="alert alert-info">
                    Account Id : [{!! $data['up_user'] !!}]
                    Account Name : [{!! $user->account_name !!}] <br>
                    Access Key : [{!! $front->dev_key !!}]
                    Access Secret : [{!! $front->dev_secret !!}]
                </p>
            @endif
            <div class="form-group">
                {!! Form::label('up_user', '新 Up User') !!}
                {!! Form::text('up_user','', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::button('设置', ['class' => 'btn btn-info', 'type'=>'submit']) !!}
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-sm-6">
            <pre id="result" style="display: none;"></pre>
        </div>
    </div>
    <script>
        require(['jquery', 'lemon/util', 'json', 'jquery.validate'], function ($, util, JSON) {
            $(function () {
                var conf = util.validate_conf({
                    submitHandler: function (form) {
                        $(form).ajaxSubmit({
                            success: function (data) {
                                $('#result').text(
                                        JSON.stringify(data, null, '  ')
                                ).show(300);
                            }
                        });
                    },
                    rules        : {
                        up_user: {required: true}
                    }
                }, 'bt3_ajax');
                $('#form_login').validate(conf);
            })
        })
    </script>
@endsection