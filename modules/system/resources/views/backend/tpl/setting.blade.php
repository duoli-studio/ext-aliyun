@extends('system::backend.tpl.default')
@section('backend-breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{!! $title !!}</h2>
            <ol class="breadcrumb">
                <li> {!! $description !!} </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <div class="title-action">
                @if(count($pages)>1)
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            {!! $title !!}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach($pages as $key => $conf)
                                <li>
                                    <a href="{!! route('backend:home.setting', [$key]) !!}"
                                       class="btn {!! $key == $path ? 'btn-primary' : 'btn-default' !!}">
                                        {!! $conf['initialization']['name'] !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('backend-main')
    <div class="ibox">
        <div class="ibox-content">
            <div role="tabpanel">
                <!-- Nav tabs-->
                <ul role="tablist" class="nav nav-tabs">
					<?php $i = 0 ?>
                    @foreach($tabs as $group_key => $group)
                        <li role="presentation" class="<?php echo($i++ == 0 ? 'active' : ''); ?>">
                            <a href="#{!! $group_key !!}" aria-controls="{!! $group_key !!}" role="tab"
                               data-toggle="tab">
                                {!! isset($group['title']) ? $group['title'] : '其他'  !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="tab-content mt15">
				<?php $i = 0 ?>
                @foreach($tabs as $group_key => $group)
                    <div role="tabpanel" class="tab-pane <?php echo($i++ == 0 ? 'active' : ''); ?>"
                         id="{!! $group_key !!}">
                        {!! Form::open(['url' => $url,'id' => 'form_'.$group_key, 'class' => 'form-horizontal']) !!}
                        {!! Form::hidden('_group', $group_key) !!}
                        @foreach($group['fields'] as $item_key => $item)

                            <div class="form-group">
                                <label for="" class="col-lg-2 control-label">
                                    {!! $item['label'] !!}
                                </label>
                                <div class="col-lg-10">
                                    @if ($item['type'] == 'input')
                                        {!! Form::text($item['name'], $item['value'], $item['options']) !!}
                                    @endif
                                    @if ($item['type'] == 'switch')
                                        <label class="radio-inline">
                                            {!! Form::radio($item['name'],0, $item['value'], $item['options']) !!}
                                            否
                                        </label>
                                        <label class="radio-inline">
                                            {!! Form::radio($item['name'],1, $item['value'], $item['options']) !!}
                                            是
                                        </label>
                                    @endif
                                    @if ($item['type'] == 'radio')
                                        @foreach($item['opinions'] as $_value => $_label)
                                            <label class="radio-inline">
                                                {!! Form::radio($item['name'],$_value, $item['value']== $_value, $item['options']) !!}
                                                {!! $_label !!}
                                            </label>
                                        @endforeach
                                    @endif
                                    @if ($item['type'] == 'textarea')
                                        {!! Form::textarea($item['name'], $item['value'], $item['options']) !!}
                                    @endif
                                    @if (isset($item['description']) && trim($item['description']))
                                        <p style="margin-top: 5px;color: #b1b0b0;">{!! $item['description'] !!}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                {!! Form::button('提交',['class'=>'btn btn-info J_submit', 'type'=>'submit']) !!}
                            </div>
                        </div>
                        {!!Form::close()!!}
                        <script>
						requirejs(['jquery', 'poppy/util', 'jquery.validation'], function($, util) {
							$(function() {
								$('#form_' + '{!! $group_key !!}').validate();
							})
						})
                        </script>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection