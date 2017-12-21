@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['url' => route('dsk_platform_employee.exception', [$order_id]), 'id' => 'form_progress']) !!}
    <table class="table">
        <tr>
            <td class="">异常类型:</td>
            <td> {!! Form::select('tpl_id', \App\Models\PlatformOrder::kvExceptionType(), null, ['placeholder' => '所有','class' => 'form-control w96']) !!}</td>
        </tr>
        <tr>
            <td class="w108">说明:</td>
            <td>
                {!! Form::textarea('message', null, ['class' => 'form-control', 'rows' => '3']) !!}
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button class=" btn btn-info  J_delay" type="submit"><span>提交异常</span></button>
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
    <script>
        require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
            var btn_selector = '#form_update_progress button:submit';
            var conf = util.validate_conf({
                submitHandler : function (form) {
                    util.button_interaction(btn_selector);
                    $(btn_selector).html('更新中...');
                    $(form).ajaxSubmit({
                        success : util.splash
                    });
                },
                rules : {
                    message : {
                        required : true
                    },
                    tpl_id : {
                        required : true
                    }
                },
                ignore : '.ignore',
                messages : {
                    tpl_id : {
                        required : '请选择类型!'
                    }
                }
            });
            $('#form_progress').validate(conf);
        });
    </script>
@endsection