@extends('system::tpl.develop')
@section('develop-main')
    <div class="container">
        {!! phpinfo() !!}
    </div>
@endsection