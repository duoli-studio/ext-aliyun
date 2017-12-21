@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_employee.money_header')
    <div class="panel panel-default">
        <div class="panel-body">
            <!-- 数据表格 -->
            <table class="table">
                <tr>
                    <th class="w120">完成订单总额</th>
                    <th class="w120">代练中订单总额</th>
                    <th class="w108">订单总额</th>
                </tr>
                <tr>
                    <td class="text-danger">{{ $money_over }}</td>
                    <td class="text-danger">{{ $money_handle }}</td>
                    <td class="text-danger"> {{ $money_total }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection