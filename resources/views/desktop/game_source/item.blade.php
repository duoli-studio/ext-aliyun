@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.game_source.header')
    <div class="panel panel-default">
        <div class="panel-body">
            @if (isset($item))
                {!! Form::model($item,['route' => ['dsk_game_source.edit', $item->source_id], 'id' => 'form_item', 'class' => 'form-horizontal']) !!}
            @else
                {!! Form::open(['route' => 'dsk_game_source.create','id' => 'form_item', 'class' => 'form-horizontal']) !!}
            @endif
            <div class="form-group">
                {!! Form::label('source_name', '游戏来源名称', ['class' => 'col-lg-2 control-label strong validation']) !!}
                <div class="col-lg-2">
                    {!! Form::text('source_name', null,['class' => 'form-control']) !!}
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
						    rules   : {
							    source_name: {
								    required: true,
								    remote  : "{{route('support_validate.game_source_available', [isset($item) ? $item->source_id : ''])}}"
							    }
						    },
						    messages: {
							    source_name: {
								    remote: "来源重复!"
							    }
						    }
					    }, 'bt3')
						;
					$('#form_item').validate(conf);
				});
            </script>
        </div>
@endsection