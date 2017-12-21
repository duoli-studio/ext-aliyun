@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['url' => route('dsk_platform_employee.assign_pc', [$order_id]), 'id' => 'form_progress']) !!}
    <table class="table">
        <tr>
            <td class="w48">说明:</td>
            <td>
                {!! Form::text('message', null, ['class' => 'form-control w256']) !!}
            </td>
        </tr>
        <tr>
            <td>
                <button class=" btn btn-info text-info J_delay" type="submit"><span>分配到电脑</span></button>
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
                },
                ignore : '.ignore',
                messages : {
                    message : {
                        required : '请填写要分配到哪台电脑!'
                    }
                }
            });
            $('#form_progress').validate(conf);
        });
    </script>
@endsection