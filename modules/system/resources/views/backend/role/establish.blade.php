@extends('system::backend.tpl.dialog')
@section('backend-main')
    @if (isset($item))
        {!! Form::model($item,['route' => ['backend:role.establish', $item->id], 'id' => 'form_role','class' => 'form-horizontal']) !!}
    @else
        {!! Form::open(['id' => 'form_role','class' => 'form-horizontal']) !!}
    @endif
    <div class="form-group">
        {!! Form::label('title', '角色名称', ['class' => 'col-lg-2 control-label strong validation']) !!}
        <div class="col-lg-2">
            {!! Form::text('title', null,['class'=>'form-control']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('name', '角色标识', ['class' => 'col-lg-2 control-label strong validation']) !!}
        (选定保存后不可编辑)
        <div class="col-lg-2">
            @if (isset($item))
                {!! Form::text('name', null,[
                    'class'    => 'form-control',
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ]) !!}
            @else
                {!! Form::text('name', null,['class'=>'form-control']) !!}
            @endif
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('guard', '角色组', ['class' => 'col-lg-2 control-label strong validation']) !!}
        (选定保存后不可编辑)
        <div class="col-lg-2">
            @if (isset($item))
                {!!Form::select('guard', \System\Models\PamAccount::kvType(),  $item->type,[
                    'class'    => 'form-control',
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ])!!}
            @else
                {!!Form::select('guard', \System\Models\PamAccount::kvType(), \Request::input('type'),['class'=>'form-control'])!!}
            @endif
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-2 col-lg-offset-2">
            {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info J_submit', 'type'=> 'submit']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection