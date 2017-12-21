@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.pam_role.header')
    <div class="panel panel-default">
        <div class="panel-body">
            @if (isset($item))
                {!! Form::model($item,['route' => ['dsk_pam_role.edit', $item->id], 'id' => 'form_role','class' => 'form-horizontal']) !!}
            @else
                {!! Form::open(['route' => 'dsk_pam_role.create','id' => 'form_role','class' => 'form-horizontal']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('role_name', '角色标识', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::text('role_name', null,['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('role_title', '角色名称', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::text('role_title', null,['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('account_type', '角色组', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!!Form::select('account_type', \App\Models\PamAccount::accountTypeLinear(), !isset($item) ? \Request::input('type') : $item['account_type'],['class'=>'form-control'])!!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('', '', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection