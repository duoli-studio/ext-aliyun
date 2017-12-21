@extends('lemon.template.desktop_angle')
@section('desktop-main')
		@include('desktop.game_type.header')
        <div class="panel panel-default">
            <div class="panel-body">
		@if (isset($item))
			{!! Form::model($item,['route' => ['dsk_game_type.edit', $item->type_id], 'id' => 'form_game_type','class' => 'form-horizontal']) !!}
		@else
			{!! Form::open(['route' => 'dsk_game_type.create','id' => 'form_game_type','class' => 'form-horizontal']) !!}
		@endif
            <div class="form-group">
                {!! Form::label('game_id', '游戏名称', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!!Form::select('game_id', $games, null)!!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('type_title', '游戏类型', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::text('type_title', null) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('', '', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                </div>
            </div>
		{!! Form::close() !!}
		<script>
		require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
			var conf = util.validate_conf({
				rules : {
					game_id : {
						required : true
					},
					type_title : {
						required : true
					}
				}
			}, 'bt3');
			$('#form_game_type').validate(conf);
		});
		</script>
	</div>
@endsection