@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.game_name.header')
    @if (isset($item))
        {!! Form::model($item,['route' => ['dsk_game_name.edit', $item->game_id], 'id' => 'form_game_name','class' => 'form-horizontal']) !!}
    @else
        {!! Form::open(['route' => 'dsk_game_name.create','id' => 'form_game_name','class' => 'form-horizontal']) !!}
        @endif
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    {!! Form::label('game_name', '游戏名称', ['class' => 'col-lg-2 control-label strong '.(!isset($item) ? 'validation' : '')]) !!}
                    <div class="col-lg-2">
                        {!! Form::text('game_name', null,['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('', '', ['class' => 'col-lg-2 control-label strong '.(!isset($item) ? 'validation' : '')]) !!}
                    <div class="col-lg-2">
                        {!! Form::button((isset($item) ? '编辑' : '添加'), ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                    </div>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
        <script>
			require(['jquery', 'lemon/util', 'jquery.validate', 'jquery.form'], function ($, util) {
				var conf = util.validate_conf({
					rules   : {
						game_name: {
							required: true,
							remote  : "{{route('support_validate.game_name_available', [isset($item) ? $item->game_id : ''])}}"
						}
					},
					messages: {
						game_name: {
							remote: "来源重复!"
						}
					}
				}, 'bt3');
				$('#form_game_name').validate(conf);
			});
        </script>
@endsection