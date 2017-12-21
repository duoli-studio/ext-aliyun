@extends('web.inc.tpl_dialog')
@section('tpl-main')
    <div class="mb40">
        @if (isset($item))
            {!! Form::model($item,['route' => ['web:prd.popup', $item->id]]) !!}
        @else
            {!! Form::open() !!}
        @endif
        {!! Form::hidden('book_id', $book_id) !!}
        <div class="form-group">
            <div class="mt8 text-center pt20 pd20">
                <i class="iconfont icon-document" style="font-size: 100px;"></i>
            </div>
            <div class="mt8">
                {!! Form::text('title', null, ['class' => 'form-control', 'placeholder'=>'文档名称, 例如: XXXX产品文档']) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::tree('parent_id', $items, (isset($item) ? $item->parent_id : (\Input::get('parent_id'))), ['placeholder'=> '请选择上级文档', 'class'=> 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::button(isset($item) ? '编辑' : '保存', ['class' => 'form-control btn-success J_submit', 'type'=> 'submit']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection