@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    <div class="page">
        @include('desktop.platform_order.header_detail')
        <div>
            @if ($pictures->total())
                <table class="table table-bordered">
                    <tr>
                        <th class="w144">时间:</th>
                        <td>说明</td>
                        <td>截图</td>
                        <td>上传者</td>
                    </tr>
                    @foreach($pictures as $picture)
                        <tr>
                            <th>{!! $picture->created_at !!}</th>
                            <td>{!! $picture->pic_desc !!}</td>
                            <td>
                                @if ($picture->pic_screen)
                                    {!! Form::showThumb($picture->pic_screen, ['height'=>30]) !!}
                                @endif
                            </td>
                            <td>{!! $picture->account_name !!}</td>
                        </tr>
                    @endforeach
                </table>
                @if ($pictures->hasPages())
                    <div class="clearfix">
                        <div class="pull-right">{!!$pictures->render()!!}</div>
                    </div>
                @endif
            @else
                @include('desktop.inc.empty')
            @endif
        </div>
    </div>
@endsection