@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.game_server.header')
    <div class="panel panel-default">
        <div class="panel-body">
            @if (isset($item))
                {!! Form::model($item,['route' => ['dsk_game_server.edit', $item->server_id], 'id' => 'form_game_server','class' => 'form-horizontal']) !!}
            @else
                {!! Form::open(['route' => 'dsk_game_server.create','id' => 'form_game_server','class' => 'form-horizontal']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('game_id', '游戏名称', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!!Form::select('game_id', $games, null, ['id' => 'game_id','class' => 'form-control'])!!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('parent_id', '上级菜单', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2" id="parent_id_ctr">
                    {!!Form::tree('parent_id', $categoryTree, null, ['placeholder' => '作为一级菜单','class' => 'form-control'])!!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('server_title', '服务器标题', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::text('server_title', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('is_enable', '是否启用', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::radio('is_enable','Y',['class' => 'form-control radio-inline']) !!}是
                    {!! Form::radio('is_enable', 'N',['class' => 'form-control radio-inline']) !!}否
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plat_yidailian', '易代练', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::text('plat_yidailian', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plat_yqdailian', '17代练', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::text('plat_yqdailian', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plat_dailianmao', '代练猫', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::text('plat_dailianmao', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plat_dailiantong', '代练通', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::text('plat_dailiantong', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plat_dianjingbaozi', '电竞包子', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::text('plat_dianjingbaozi', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('plat_tencent', '腾讯代码', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::text('plat_tencent', null,['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('', '', ['class' => 'col-lg-2 control-label strong place']) !!}
                <div class="col-lg-2">
                    {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                </div>
            </div>
            {!! Form::close() !!}
            <script>
				require(['jquery', 'fadan/util', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, helper, util) {
					var conf = util.validate_conf({
						rules: {
							game_id     : {
								required: true
							},
							server_title: {
								required: true
							}
						}
					}, 'bt3');
					$('#form_game_server').validate(conf);
				});
            </script>
        </div>
@endsection