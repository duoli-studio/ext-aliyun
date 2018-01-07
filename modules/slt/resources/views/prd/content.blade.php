@extends('slt::inc.tpl')
@section('tpl-main')
    <style>
        html {
            width  : 100%;
            height : 100%;
        }
    </style>
    @if (isset($item))
        {!! Form::model($item,['route' => ['web:prd.content', $item->id], 'id' => 'form_md', 'style'=> 'height:100%']) !!}
    @else
        {!! Form::open(['route' => 'web:prd.create','id' => 'form_md', 'style'=> 'height:100%']) !!}
    @endif
    {!! Form::hidden('parent_id', $parent_id) !!}
    {!! Form::hidden('top_parent_id', $top_parent_id) !!}
    {!! Form::hidden('prd_title', $title) !!}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb pull-left">
                    <li><a href="#">编辑文档</a></li>
                    @if ($titles)
                        @foreach($titles as $k => $t)
                            <li><a href="#">{!! $t->title !!}</a></li>
                        @endforeach
                    @endif
                    @if ($_route == 'web:prd.create')
                        <li class="active">{!! $title !!}</li>
                    @endif
                </ol>
                <div class="pull-right pt10 pr8">
                    {!! Form::button('保存', ['class' => 'btn btn-success btn-sm pull-right ml10 J_submit', 'type'=> 'submit']) !!}
                    {!! Form::button('取消', ['class' => 'btn btn-default btn-sm pull-right', 'type'=> 'button', 'onclick'=>'javascript:history.go(-1)']) !!}
                </div>
            </div>
        </div>
        {!! Form::editormd('content', null, ['id'=> 'editormd']) !!}
    </div>
    {!! Form::hidden('title', isset($item) ? null : Input::input('title'), ['class' => 'form-control J_submit', 'placeholder'=>'文档标题']) !!}
    {!! Form::close() !!}
    @include('web.inc.footer')
@endsection