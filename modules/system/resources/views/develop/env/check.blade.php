<style type="text/css">
    .a1 {
        color      : red;
        text-align : center;
    }
</style>
@extends('system::tpl.develop')
@section('develop-main')
    <table class="table">
        @foreach($env as $key => $value)
            <tr>
                <th>{!! $value['title'] !!}</th>
                <td>
                    {!! $value['result'] ? '<span class="text-success">Success</span>' :'<span class="text-error">Error</span>' !!}
                </td>
            </tr>
        @endforeach
    </table>
@endsection