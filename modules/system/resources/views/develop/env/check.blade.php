<style type="text/css">
    .a1 {
        color         : red;
        text-align    : center;
    }
</style>
@extends('system::tpl.develop')
@section('develop-main')
    <table class="table" border="1" cellspacing="0">
        <tr>
            <td bgcolor="aqua"><span class="a1">weixin</span></td>
            <td bgcolor="#7fffd4">
                <button>{!! $env['weixin'] ? 'Yes' : 'No' !!}</button>
            </td>
        </tr>
        <tr>
            <td bgcolor="aqua"><span class="a1">alipay</span></td>
            <td bgcolor="#7fffd4">
                <button>{!! $env['alipay'] ? 'Yes' : 'No' !!}</button>
            </td>
        </tr>
        <tr>
            <td bgcolor="aqua"><span class="a1">yunxin</span></td>
            <td bgcolor="#7fffd4">
                <button>{!! $env['yunxin'] ? 'Yes' : 'No' !!}</button>
            </td>
        </tr>
    </table>
@endsection