@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['url' => route('dsk_platform_employee.update_progress', [$order_id]), 'id' => 'form_progress']) !!}
    <table class="table">
        <tr>
            <td class="w108">图片:</td>
            <td>{!! Form::thumb('pic_screen', null) !!}</td>
        </tr>
        <tr>
            <td class="w108">说明:</td>
            <td>
                {!! Form::textarea('pic_desc', null, ['class' => 'form-control', 'rows' => '3']) !!}
            </td>
        </tr>
        <tr>
            <td>
                <button class=" btn btn-info J_delay" type="submit"><span>更新文件</span></button>
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
                    pic_desc : {
                        required : true
                    },
                    pic_screen : {
                        required : true
                    }
                },
                ignore : '.ignore',
                messages : {
                    pic_screen : {
                        required : '请上传进度图!'
                    }
                }
            });
            $('#form_progress').validate(conf);
        });
    </script>
@endsection