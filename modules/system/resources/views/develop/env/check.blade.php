@extends('system::tpl.develop')
@section('develop-main')
    <div class="container">
        <table class="table">
            <tr>
                <td>weixin</td>
                <td>{!! $env['weixin'] ? 'Yes' : 'No' !!}</td>
            </tr>
        </table>
    </div>
@endsection