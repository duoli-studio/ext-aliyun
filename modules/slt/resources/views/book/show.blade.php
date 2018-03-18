@extends('slt::tpl.default')
@section('tpl-main')
    <div class="container ">
        <div class="row">
            <div class="col-md-12">
                <div class="sour--my_title clearfix mt15">
                    <h3 class="pull-left">文档库 {!! $book->title !!}</h3>
                    <div class="pull-right">
                        <a class="J_iframe btn btn-success btn-sm pull-right" data-width="400" data-height="444"
                           data-title="创建Prd文档"
                           href="{!! route_url('slt:article.popup', [], ['book_id'=>$book->id]) !!}">
                            <i class="iconfont icon-add"></i>
                            创建新文档
                        </a>
                    </div>
                </div>
                <div class="prd_my--book_item">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="book_item--side">
                                <ul>
                                    <li>标题 : {!! $book->title !!}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="book_item--content">
                                {!! Form::open() !!}
                                <table width="98%" border="0" cellpadding="5" cellspacing="1" class="table">
                                    <tr>
                                        <th class="w72">排序</th>
                                        <th class="w72">ID</th>
                                        <th>标题</th>
                                        <th class="w216">操作</th>
                                    </tr>
                                    {!! $html_tree !!}
                                    <tr>
                                        <td colspan="7" valign="middle">
                                            <button class="btn btn-info btn-sm J_submit" type="submit"><span>排序</span></button>
                                        </td>
                                    </tr>
                                </table>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('slt::tpl.inc_footer')
@endsection