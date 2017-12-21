@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['route' => 'dsk_platform_status_yi.progress', 'id' => 'form_picture', 'class'=> 'form-horizontal form-group-sm','files'=>true]) !!}
    {!! Form::hidden('status_id', $status_id) !!}
    <div class="form-group">
        <label class="col-sm-2 control-label">进度图: </label>
        <div class="col-sm-10">
            {!! Form::file('picture', null) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">说明: </label>
        <div class="col-sm-10">
            {!! Form::text('message', null, ['class'=> 'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button class=" btn btn-primary J_delay J_submit" type="submit">更新文件</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection